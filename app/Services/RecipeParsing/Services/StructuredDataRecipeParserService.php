<?php

namespace App\Services\RecipeParsing\Services;

use App\Services\RecipeParsing\Contracts\HtmlFetcherInterface;
use App\Services\RecipeParsing\Contracts\RecipeParserInterface;
use App\Services\RecipeParsing\Data\ParsedRecipeData;
use App\Services\RecipeParsing\Data\ParserResult;
use App\Services\RecipeParsing\Exceptions\RecipeParsingException;
use Brick\StructuredData\HTMLReader;
use Brick\StructuredData\Item;
use Brick\StructuredData\Reader\JsonLdReader;
use Brick\StructuredData\Reader\MicrodataReader;
use Brick\StructuredData\Reader\RdfaLiteReader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StructuredDataRecipeParserService implements RecipeParserInterface
{
    private array $recipeData = [
        'title' => null,
        'url' => null,
        'keywords' => null,
        'ingredients' => null,
        'steps' => null,
        'yield' => null,
        'prepTime' => null,
        'cookTime' => null,
        'totalTime' => null,
        'images' => null,
        'description' => null,
    ];

    public function __construct(
        private readonly HtmlFetcherInterface $htmlFetcher
    ) {}

    public function parse(string $url): ?ParserResult
    {
        $startTime = microtime(true);

        try {
            Log::info('Starting structured data recipe parsing', [
                'url' => $url,
                'user_id' => Auth::id(),
            ]);

            $html = $this->htmlFetcher->fetchForStructuredData($url);
            $this->recipeData['url'] = $url;

            $readers = [
                'JsonLdReader' => new HTMLReader(new JsonLdReader),
                'MicrodataReader' => new HTMLReader(new MicrodataReader),
                'RdfaLiteReader' => new HTMLReader(new RdfaLiteReader),
            ];

            foreach ($readers as $readerName => $reader) {
                $items = $reader->read($html, $url);

                Log::debug("Trying {$readerName} structured data reader", [
                    'url' => $url,
                    'reader' => $readerName,
                    'items_count' => count($items),
                    'user_id' => Auth::id(),
                ]);

                $parsed = $this->parseItems($items, $url);
                if ($parsed) {
                    $result = new ParserResult($parsed);
                    $totalDuration = (microtime(true) - $startTime) * 1000;

                    Log::info('Recipe successfully parsed from structured data', [
                        'url' => $url,
                        'reader_used' => $readerName,
                        'recipe_title' => $result->recipe->title,
                        'total_processing_time_ms' => round($totalDuration, 2),
                        'user_id' => Auth::id(),
                    ]);

                    return $result;
                }
            }

            Log::warning('Structured data parsing failed - no recipe items found', [
                'url' => $url,
                'user_id' => Auth::id(),
            ]);

            return null;
        } catch (\Exception $e) {
            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::error('Structured data recipe parsing failed', [
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
                message: 'Structured data parsing failed: '.$e->getMessage(),
                previous: $e,
                url: $url,
                parser: $this->getName()
            );
        }
    }

    public function isAvailable(): bool
    {
        return true; // No API keys required for structured data parsing
    }

    public function getName(): string
    {
        return 'Structured Data';
    }

    public function getIdentifier(): string
    {
        return 'structured-data';
    }

    private function parseItems(array $items, string $url): ?ParsedRecipeData
    {
        try {
            // Reset recipe data for this attempt
            $this->resetRecipeData($url);

            // First, try to find items that explicitly contain 'recipe'
            foreach ($items as $item) {
                if (Str::contains($this->getTypesLower($item), 'recipe')) {
                    $result = $this->parseItem($item);

                    if ($result?->isValid()) {
                        Log::info('Recipe successfully parsed from structured data (recipe type found)', [
                            'url' => $url,
                            'recipe_title' => $result->title,
                            'user_id' => Auth::id(),
                        ]);

                        return $result;
                    }
                }
            }

            // Fallback: if there's only one item, try parsing it anyway
            if (count($items) === 1) {
                $result = $this->parseItem($items[0]);

                if ($result?->isValid()) {
                    Log::info('Recipe successfully parsed from structured data (single item fallback)', [
                        'url' => $url,
                        'recipe_title' => $result->title,
                        'user_id' => Auth::id(),
                    ]);

                    return $result;
                } else {
                    Log::warning('Structured data parsing failed - single item did not contain recipe', [
                        'url' => $url,
                        'item_types' => $this->getTypesLower($items[0]),
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to parse structured data items', [
                'url' => $url,
                'items_count' => count($items),
                'error_message' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return null;
        }
    }

    private function parseItem(Item $item): ?ParsedRecipeData
    {
        try {
            Log::debug('Parsing individual structured data item', [
                'url' => $this->recipeData['url'],
                'item_types' => $this->getTypesLower($item),
                'properties_count' => count($item->getProperties()),
                'user_id' => Auth::id(),
            ]);

            $properties = $item->getProperties();
            $processedProperties = [];

            foreach ($properties as $name => $values) {
                try {
                    $methodName = 'parse_'.$this->sanitizeName($name);
                    if (method_exists($this, $methodName)) {
                        $this->$methodName($values);
                        $processedProperties[] = $name;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to parse structured data property', [
                        'url' => $this->recipeData['url'],
                        'property_name' => $name,
                        'property_method' => $methodName,
                        'error_message' => $e->getMessage(),
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            $parsedData = ParsedRecipeData::fromArray($this->recipeData);

            if (! $parsedData->isValid()) {
                $missingFields = [];
                if (empty($this->recipeData['title'])) {
                    $missingFields[] = 'title';
                }
                if (empty($this->recipeData['ingredients']) || ! is_array($this->recipeData['ingredients'])) {
                    $missingFields[] = 'ingredients';
                }
                if (empty($this->recipeData['steps']) || ! is_array($this->recipeData['steps'])) {
                    $missingFields[] = 'steps';
                }

                Log::warning('Structured data parsing failed - required fields missing', [
                    'url' => $this->recipeData['url'],
                    'missing_fields' => $missingFields,
                    'processed_properties' => $processedProperties,
                    'recipe_data_keys' => array_keys(array_filter($this->recipeData)),
                    'user_id' => Auth::id(),
                ]);

                return null;
            }

            Log::info('Structured data recipe parsing completed successfully', [
                'url' => $this->recipeData['url'],
                'recipe_title' => $this->recipeData['title'],
                'processed_properties' => $processedProperties,
                'extracted_fields' => array_keys(array_filter($this->recipeData)),
                'user_id' => Auth::id(),
            ]);

            return $parsedData;
        } catch (\Exception $e) {
            Log::error('Structured data recipe parsing failed', [
                'url' => $this->recipeData['url'],
                'item_types' => $this->getTypesLower($item),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return null;
        }
    }

    private function resetRecipeData(string $url): void
    {
        $this->recipeData = [
            'title' => null,
            'url' => $url,
            'keywords' => null,
            'ingredients' => [],
            'steps' => [],
            'yield' => null,
            'prepTime' => null,
            'cookTime' => null,
            'totalTime' => null,
            'images' => [],
            'description' => null,
        ];
    }

    // Property parsing methods (simplified versions of the original)
    private function parse_name($values): void
    {
        $this->recipeData['title'] = $this->cleanText($this->getFirstValue($values));
    }

    private function parse_description($values): void
    {
        $this->recipeData['description'] = $this->cleanText($this->getFirstValue($values));
    }

    private function parse_keywords($values): void
    {
        $this->recipeData['keywords'] = $this->cleanText($this->getFirstValue($values));
    }

    private function parse_recipeyield($values): void
    {
        $this->recipeData['yield'] = $this->cleanText($this->getFirstValue($values));
    }

    private function parse_preptime($values): void
    {
        $this->recipeData['prepTime'] = $this->cleanText($this->getFirstValue($values));
    }

    private function parse_cooktime($values): void
    {
        $this->recipeData['cookTime'] = $this->cleanText($this->getFirstValue($values));
    }

    private function parse_totaltime($values): void
    {
        $this->recipeData['totalTime'] = $this->cleanText($this->getFirstValue($values));
    }

    private function parse_image($values): void
    {
        try {
            foreach ($values as $item) {
                if ($item instanceof Item) {
                    $this->processItemImage($item);
                } elseif (is_string($item) && $this->isValidUrl($item)) {
                    $this->addImage($item);
                } elseif (is_array($item)) {
                    foreach ($item as $imageUrl) {
                        if (is_string($imageUrl) && $this->isValidUrl($imageUrl)) {
                            $this->addImage($imageUrl);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse image data from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'values_type' => gettype($values),
                'user_id' => Auth::id(),
            ]);
        }
    }

    private function parse_recipeingredient($values): void
    {
        try {
            if (is_array($values)) {
                $this->recipeData['ingredients'] = Collection::make($values)
                    ->transform(fn ($item) => $this->cleanText($item))
                    ->all();

                Log::debug('Successfully parsed recipe ingredients', [
                    'url' => $this->recipeData['url'],
                    'ingredients_count' => count($this->recipeData['ingredients']),
                    'user_id' => Auth::id(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse recipe ingredients from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'values_type' => gettype($values),
                'user_id' => Auth::id(),
            ]);
        }
    }

    private function parse_recipeinstructions(array $items): void
    {
        try {
            foreach ($items as $item) {
                if ($item instanceof Item) {
                    $this->processInstructionItem($item);
                } else {
                    $this->recipeData['steps'][] = $this->cleanText($item);
                }
            }

            Log::debug('Successfully parsed recipe instructions', [
                'url' => $this->recipeData['url'],
                'steps_count' => is_array($this->recipeData['steps']) ? count($this->recipeData['steps']) : 0,
                'user_id' => Auth::id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to parse recipe instructions from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'items_count' => count($items),
                'user_id' => Auth::id(),
            ]);
        }
    }

    // Helper methods
    private function processInstructionItem(Item $item): void
    {
        $types = $this->getTypesLower($item);

        if (Str::contains($types, 'howtosection')) {
            $this->processHowToSection($item->getProperties());
        }

        if (Str::contains($types, 'howtostep')) {
            $this->processHowToStep($item->getProperties());
        }
    }

    private function processHowToSection(array $properties): void
    {
        foreach ($properties as $name => $values) {
            $name = $this->sanitizeName($name);

            if ($name === 'name') {
                $this->recipeData['steps'][] = '<p><strong>'.$this->cleanText($values[0]).'</strong></p>';
            }

            if ($name === 'itemlistelement') {
                $this->processItemListElement($values);
            }
        }
    }

    private function processHowToStep(array $properties): void
    {
        foreach ($properties as $name => $values) {
            $name = $this->sanitizeName($name);

            if ($name === 'text') {
                $this->recipeData['steps'][] = $this->cleanText($values[0]);
            }
        }
    }

    private function processItemListElement(array $values): void
    {
        $this->recipeData['steps'][] = '<ol>';

        foreach ($values as $value) {
            if ($value instanceof Item) {
                $this->processItemElement($value->getProperties());
            }
        }

        $this->recipeData['steps'][] = '</ol>';
    }

    private function processItemElement(array $properties): void
    {
        foreach ($properties as $name => $values) {
            $name = $this->sanitizeName($name);

            if ($name === 'text') {
                $this->recipeData['steps'][] = '<li>'.$this->cleanText($values[0]).'</li>';
            }
        }
    }

    private function processItemImage(Item $item): void
    {
        foreach ($item->getProperties() as $name => $values) {
            $name = $this->sanitizeName($name);

            if ($name === 'url' && $this->isValidUrl($values[0])) {
                $this->addImage($values[0]);
            }
        }
    }

    private function getTypesLower(Item $item): string
    {
        return Str::lower(implode(',', $item->getTypes()));
    }

    private function addImage(string $url): void
    {
        $this->recipeData['images'][] = $url;
    }

    private function isValidUrl(string $value): bool
    {
        return Str::contains($value, ['http://', 'https://']);
    }

    private function getFirstValue($values)
    {
        return is_array($values) ? $values[0] : $values;
    }

    private function sanitizeName(string $name): string
    {
        return Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));
    }

    private function cleanText(string $text): string
    {
        // First ensure the text is properly UTF-8 encoded
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        
        // Decode HTML entities
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
