<?php

namespace App\Services\RecipeParsing\Services;

use App\Services\RecipeParsing\Contracts\RecipeParserInterface;
use App\Services\RecipeParsing\Data\ParsedRecipeData;
use App\Services\RecipeParsing\Exceptions\RecipeParsingException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RecipeParsingService
{
    /** @var Collection<RecipeParserInterface> */
    private Collection $parsers;

    public function __construct()
    {
        $this->parsers = collect();
    }

    /**
     * Register a parser with this service.
     */
    public function registerParser(RecipeParserInterface $parser): self
    {
        $this->parsers->put($parser->getIdentifier(), $parser);
        return $this;
    }

    /**
     * Get all available parsers (those with proper API keys/config).
     */
    public function getAvailableParsers(): Collection
    {
        return $this->parsers->filter(fn(RecipeParserInterface $parser) => $parser->isAvailable());
    }

    /**
     * Parse a recipe from a URL using the specified parser.
     */
    public function parseWithParser(string $url, string $parserIdentifier): ?ParsedRecipeData
    {
        $startTime = microtime(true);

        $parser = $this->parsers->get($parserIdentifier);

        if (!$parser) {
            throw new RecipeParsingException(
                message: "Parser '{$parserIdentifier}' not found",
                url: $url,
                parser: $parserIdentifier
            );
        }

        if (!$parser->isAvailable()) {
            throw new RecipeParsingException(
                message: "Parser '{$parserIdentifier}' is not available (missing API key or configuration)",
                url: $url,
                parser: $parserIdentifier
            );
        }

        try {
            Log::info("Try recipe parsing with {$parser->getName()}", [
                'url' => $url,
                'parser' => $parser->getName(),
                'parser_identifier' => $parserIdentifier,
                'user_id' => Auth::id(),
            ]);

            $result = $parser->parse($url);

            $totalDuration = (microtime(true) - $startTime) * 1000;

            if ($result) {
                Log::info('Recipe parsing completed successfully', [
                    'url' => $url,
                    'parser' => $parser->getName(),
                    'recipe_title' => $result->title,
                    'total_processing_time_ms' => round($totalDuration, 2),
                    'user_id' => Auth::id(),
                ]);
            } else {
                Log::warning('Recipe parsing returned no results', [
                    'url' => $url,
                    'parser' => $parser->getName(),
                    'total_processing_time_ms' => round($totalDuration, 2),
                    'user_id' => Auth::id(),
                ]);
            }

            return $result;

        } catch (RecipeParsingException $e) {
            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::error('Recipe parsing failed with specific parser', [
                'url' => $url,
                'parser' => $parser->getName(),
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'total_processing_time_ms' => round($totalDuration, 2),
                'user_id' => Auth::id(),
            ]);

            throw $e;
        }
    }

    /**
     * Try parsing with multiple parsers in order of preference until one succeeds.
     */
    public function parseWithFallback(string $url, array $preferredOrder = ['structured-data', 'firecrawl', 'open-ai']): ?ParsedRecipeData
    {
        $startTime = microtime(true);

        Log::info('Starting recipe parsing with fallback strategy', [
            'url' => $url,
            'preferred_order' => $preferredOrder,
            'available_parsers' => $this->getAvailableParsers()->keys()->all(),
            'user_id' => Auth::id(),
        ]);

        $attempts = collect();

        foreach ($preferredOrder as $parserIdentifier) {
            $parser = $this->parsers->get($parserIdentifier);

            if (!$parser || !$parser->isAvailable()) {
                $attempts->push([
                    'parser' => $parserIdentifier,
                    'status' => 'skipped',
                    'reason' => !$parser ? 'not found' : 'not available',
                ]);
                continue;
            }

            $attemptStart = microtime(true);

            try {
                Log::info('Attempting parsing with fallback parser', [
                    'url' => $url,
                    'parser' => $parser->getName(),
                    'attempt_number' => $attempts->count() + 1,
                    'user_id' => Auth::id(),
                ]);

                $result = $parser->parse($url);
                $attemptDuration = (microtime(true) - $attemptStart) * 1000;

                if ($result) {
                    $totalDuration = (microtime(true) - $startTime) * 1000;

                    $attempts->push([
                        'parser' => $parser->getName(),
                        'status' => 'success',
                        'duration_ms' => round($attemptDuration, 2),
                    ]);

                    Log::info('Recipe parsing succeeded with fallback strategy', [
                        'url' => $url,
                        'successful_parser' => $parser->getName(),
                        'recipe_title' => $result->title,
                        'total_attempts' => $attempts->count(),
                        'total_processing_time_ms' => round($totalDuration, 2),
                        'attempts' => $attempts->all(),
                        'user_id' => Auth::id(),
                    ]);

                    return $result;
                } else {
                    $attempts->push([
                        'parser' => $parser->getName(),
                        'status' => 'no_result',
                        'duration_ms' => round($attemptDuration, 2),
                    ]);
                }

            } catch (RecipeParsingException $e) {
                $attemptDuration = (microtime(true) - $attemptStart) * 1000;

                $attempts->push([
                    'parser' => $parser->getName(),
                    'status' => 'error',
                    'error' => $e->getMessage(),
                    'duration_ms' => round($attemptDuration, 2),
                ]);

                Log::warning('Fallback parser attempt failed', [
                    'url' => $url,
                    'parser' => $parser->getName(),
                    'error_message' => $e->getMessage(),
                    'attempt_duration_ms' => round($attemptDuration, 2),
                    'user_id' => Auth::id(),
                ]);
            }
        }

        $totalDuration = (microtime(true) - $startTime) * 1000;

        Log::warning('All recipe parsing attempts failed', [
            'url' => $url,
            'total_attempts' => $attempts->count(),
            'total_processing_time_ms' => round($totalDuration, 2),
            'attempts' => $attempts->all(),
            'user_id' => Auth::id(),
        ]);

        return null;
    }

    /**
     * Get information about all registered parsers.
     */
    public function getParserInfo(): Collection
    {
        return $this->parsers->map(function (RecipeParserInterface $parser) {
            return [
                'identifier' => $parser->getIdentifier(),
                'name' => $parser->getName(),
                'available' => $parser->isAvailable(),
            ];
        });
    }

    /**
     * Check if a specific parser is available.
     */
    public function isParserAvailable(string $parserIdentifier): bool
    {
        $parser = $this->parsers->get($parserIdentifier);
        return $parser?->isAvailable() ?? false;
    }
}
