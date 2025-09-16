<?php

namespace App\Services\RecipeParsing\Services;

use App\Services\RecipeParsing\Contracts\RecipeParserInterface;
use App\Services\RecipeParsing\Data\ParsedRecipeData;
use App\Services\RecipeParsing\Exceptions\ApiKeyMissingException;
use App\Services\RecipeParsing\Exceptions\RecipeParsingException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use OpenAI\Client as OpenAIClient;

class OpenAIRecipeParserService implements RecipeParserInterface
{
    public function __construct(
        private readonly ?OpenAIClient $openAIClient,
        private readonly ?string $apiKey
    ) {
    }

    public function parse(string $url): ?ParsedRecipeData
    {
        if (!$this->isAvailable()) {
            throw new ApiKeyMissingException('OpenAI', $url);
        }

        if (!$this->openAIClient) {
            throw new ApiKeyMissingException('OpenAI', $url);
        }

        $startTime = microtime(true);

        try {
            Log::info('Making OpenAI API request for recipe parsing', [
                'url' => $url,
                'user_id' => Auth::id(),
            ]);

            $prompt = $this->buildPrompt($url);

            $result = $this->openAIClient->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $content = json_decode($result->choices[0]->message->content, true, 512, JSON_THROW_ON_ERROR);
            
            if (empty($content['title'])) {
                Log::warning('OpenAI returned empty or invalid recipe data', [
                    'url' => $url,
                    'has_title' => !empty($content['title'] ?? null),
                    'response_content' => $content,
                    'user_id' => Auth::id(),
                ]);
                return null;
            }

            $parsedData = ParsedRecipeData::fromArray(array_merge($content, ['url' => $url]));

            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::info('Recipe successfully parsed from OpenAI', [
                'url' => $url,
                'recipe_title' => $parsedData->title,
                'extracted_fields' => array_keys(array_filter($parsedData->toArray())),
                'total_processing_time_ms' => round($totalDuration, 2),
                'user_id' => Auth::id(),
            ]);

            return $parsedData;

        } catch (\JsonException $e) {
            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::error('OpenAI recipe parsing failed - invalid JSON response', [
                'url' => $url,
                'error_message' => $e->getMessage(),
                'total_processing_time_ms' => round($totalDuration, 2),
                'user_id' => Auth::id(),
            ]);

            throw new RecipeParsingException(
                message: 'OpenAI parsing failed - invalid JSON response: ' . $e->getMessage(),
                previous: $e,
                url: $url,
                parser: $this->getName()
            );
        } catch (\Exception $e) {
            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::error('OpenAI recipe parsing failed', [
                'url' => $url,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'total_processing_time_ms' => round($totalDuration, 2),
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw new RecipeParsingException(
                message: 'OpenAI parsing failed: ' . $e->getMessage(),
                previous: $e,
                url: $url,
                parser: $this->getName()
            );
        }
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey) && $this->openAIClient !== null;
    }

    public function getName(): string
    {
        return 'OpenAI';
    }

    public function getIdentifier(): string
    {
        return 'open-ai';
    }

    private function buildPrompt(string $url): string
    {
        return "Haal alleen de volgende informatie uit het recept dat je hier vindt: {$url}

            - titel (string) [title]
            - voorbereidingstijd (in minuten) [prepTime]
            - kooktijd (in minuten) [cookTime]
            - ingrediënten (array, 1 per regel) [ingredients]
            - stappen (array van strings) [steps]
            - porties (getal) [yield]
            - moeilijkheidsgraad (makkelijk, gemiddeld, moeilijk) [difficulty]
            - tags (komma gescheiden lijst van strings) [keywords]
            - introductie of samenvatting (string) [summary]

            Geef de output als geldige JSON terug.
            De sleutels moeten exact overeenkomen met de waarde tussen [] in de bovenstaande lijst.
            De waarden mogen alleen exacte tekst zijn zoals deze op de website staat.
            De waarden mogen ook leeg zijn als er geen informatie gevonden is.
            Vertaal de moeilijkheidsgraad naar het Engels (easy, average, difficult).";
    }
}