<?php

namespace App\Support;

use App\Http\Resources\Recipe\ImportResource;
use Brick\StructuredData\Item;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StructuredDataRecipeParser
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
    ];

    public function __construct($url)
    {
        $this->recipeData['url'] = $url;
    }

    public static function fromItems($items, $url): ?ImportResource
    {
        $startTime = microtime(true);

        try {
            // Log the start of structured data parsing
            Log::info('Starting structured data recipe parsing', [
                'url' => $url,
                'items_count' => count($items),
                'user_id' => optional(auth()->user())->id,
            ]);

            foreach ($items as $item) {
                if (Str::contains(self::getTypesLower($item), 'recipe')) {
                    $result = self::fromItem($item, $url);

                    if ($result !== null) {
                        $totalDuration = (microtime(true) - $startTime) * 1000;

                        Log::info('Recipe successfully parsed from structured data (recipe type found)', [
                            'url' => $url,
                            'recipe_title' => $result->title ?? null,
                            'total_processing_time_ms' => round($totalDuration, 2),
                            'user_id' => optional(auth()->user())->id,
                        ]);
                    }

                    return $result;
                }
            }

            if (count($items) === 1) {
                $result = self::fromItem($items[0], $url);

                if ($result !== null) {
                    $totalDuration = (microtime(true) - $startTime) * 1000;

                    Log::info('Recipe successfully parsed from structured data (single item fallback)', [
                        'url' => $url,
                        'recipe_title' => $result->title ?? null,
                        'total_processing_time_ms' => round($totalDuration, 2),
                        'user_id' => optional(auth()->user())->id,
                    ]);
                } else {
                    Log::warning('Structured data parsing failed - single item did not contain recipe', [
                        'url' => $url,
                        'item_types' => self::getTypesLower($items[0]),
                        'user_id' => optional(auth()->user())->id,
                    ]);
                }

                return $result;
            }

            Log::warning('Structured data parsing failed - no recipe items found', [
                'url' => $url,
                'items_count' => count($items),
                'user_id' => optional(auth()->user())->id,
            ]);

            return null;

        } catch (\Exception $e) {
            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::error('Structured data recipe parsing failed', [
                'url' => $url,
                'items_count' => count($items),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'total_processing_time_ms' => round($totalDuration, 2),
                'user_id' => optional(auth()->user())->id,
                'stack_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return null;
        }
    }

    private static function fromItem(Item $item, $url): ?ImportResource
    {
        try {
            Log::debug('Parsing individual structured data item', [
                'url' => $url,
                'item_types' => self::getTypesLower($item),
                'properties_count' => count($item->getProperties()),
                'user_id' => optional(auth()->user())->id,
            ]);

            return (new static($url))->parse($item);

        } catch (\Exception $e) {
            Log::error('Failed to parse structured data item', [
                'url' => $url,
                'item_types' => self::getTypesLower($item),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'user_id' => optional(auth()->user())->id,
                'stack_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return null;
        }
    }

    public function parse(Item $item): ?ImportResource
    {
        $startTime = microtime(true);

        try {
            $properties = $item->getProperties();
            $processedProperties = [];

            Log::debug('Starting to parse structured data properties', [
                'url' => $this->recipeData['url'],
                'item_types' => self::getTypesLower($item),
                'properties' => array_keys($properties),
                'user_id' => optional(auth()->user())->id,
            ]);

            foreach ($properties as $name => $values) {
                try {
                    $fn = 'parse_'.$this->sanitizeName($name);
                    if (method_exists($this, $fn)) {
                        $this->$fn($values);
                        $processedProperties[] = $name;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to parse structured data property', [
                        'url' => $this->recipeData['url'],
                        'property_name' => $name,
                        'property_method' => $fn,
                        'error_message' => $e->getMessage(),
                        'user_id' => optional(auth()->user())->id,
                    ]);
                }
            }

            // We require title, ingredients, and steps for a valid recipe.
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

            if (! empty($missingFields)) {
                $totalDuration = (microtime(true) - $startTime) * 1000;

                Log::warning('Structured data parsing failed - required fields missing', [
                    'url' => $this->recipeData['url'],
                    'missing_fields' => $missingFields,
                    'processed_properties' => $processedProperties,
                    'recipe_data_keys' => array_keys(array_filter($this->recipeData)),
                    'has_title' => ! empty($this->recipeData['title']),
                    'has_ingredients' => ! empty($this->recipeData['ingredients']) && is_array($this->recipeData['ingredients']),
                    'has_steps' => ! empty($this->recipeData['steps']) && is_array($this->recipeData['steps']),
                    'total_processing_time_ms' => round($totalDuration, 2),
                    'user_id' => optional(auth()->user())->id,
                ]);

                return null;
            }

            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::info('Structured data recipe parsing completed successfully', [
                'url' => $this->recipeData['url'],
                'recipe_title' => $this->recipeData['title'],
                'processed_properties' => $processedProperties,
                'extracted_fields' => array_keys(array_filter($this->recipeData)),
                'total_processing_time_ms' => round($totalDuration, 2),
                'user_id' => optional(auth()->user())->id,
            ]);

            return new ImportResource($this->recipeData);
        } catch (\Exception $e) {
            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::error('Structured data recipe parsing failed', [
                'url' => $this->recipeData['url'],
                'item_types' => self::getTypesLower($item),
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'total_processing_time_ms' => round($totalDuration, 2),
                'user_id' => optional(auth()->user())->id,
                'stack_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return null;
        }
    }

    private function parse_name($values): void
    {
        $this->recipeData['title'] = $this->getFirstValue($values);
    }

    private function parse_description($values): void
    {
        $this->recipeData['description'] = $this->getFirstValue($values);
    }

    private function parse_keywords($values): void
    {
        $this->recipeData['keywords'] = $this->getFirstValue($values);
    }

    private function parse_recipeyield($values): void
    {
        $this->recipeData['yield'] = $this->getFirstValue($values);
    }

    private function parse_preptime($values): void
    {
        $this->recipeData['prepTime'] = $this->getFirstValue($values);
    }

    private function parse_cooktime($values): void
    {
        $this->recipeData['cookTime'] = $this->getFirstValue($values);
    }

    private function parse_totaltime($values): void
    {
        $this->recipeData['totalTime'] = $this->getFirstValue($values);
    }

    private function parse_image($values): void
    {
        try {
            foreach ($values as $item) {
                if ($item instanceof Item) {
                    $this->processItemImage($item);
                } elseif (is_string($item) && $this->isRelativeUrl($item)) {
                    $this->addImage($item);
                } elseif (is_array($item)) {
                    Log::warning('Structured data image parsing - array of strings not yet supported', [
                        'url' => $this->recipeData['url'],
                        'image_data' => $item,
                        'user_id' => optional(auth()->user())->id,
                    ]);
                    // Convert array to strings if possible
                    foreach ($item as $imageUrl) {
                        if (is_string($imageUrl) && $this->isRelativeUrl($imageUrl)) {
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
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private function parse_recipeingredient($values): void
    {
        try {
            if (is_array($values)) {
                $this->recipeData['ingredients'] = collect($values)->transform(function ($item) {
                    return html_entity_decode($item);
                })->all();

                Log::debug('Successfully parsed recipe ingredients', [
                    'url' => $this->recipeData['url'],
                    'ingredients_count' => count($this->recipeData['ingredients']),
                    'user_id' => optional(auth()->user())->id,
                ]);
            } else {
                Log::warning('Recipe ingredients data is not an array', [
                    'url' => $this->recipeData['url'],
                    'values_type' => gettype($values),
                    'user_id' => optional(auth()->user())->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to parse recipe ingredients from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'values_type' => gettype($values),
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private function parse_recipeinstructions(array $items): void
    {
        try {
            foreach ($items as $item) {
                if ($item instanceof Item) {
                    $this->processItem($item);
                } else {
                    $this->recipeData['steps'][] = html_entity_decode($item);
                }
            }

            Log::debug('Successfully parsed recipe instructions', [
                'url' => $this->recipeData['url'],
                'steps_count' => is_array($this->recipeData['steps']) ? count($this->recipeData['steps']) : 0,
                'user_id' => optional(auth()->user())->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to parse recipe instructions from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'items_count' => count($items),
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private function processItem(Item $item): void
    {
        try {
            $types = self::getTypesLower($item);

            if (Str::contains($types, 'howtosection')) {
                $this->processHowToSection($item->getProperties());
            }

            if (Str::contains($types, 'howtostep')) {
                $this->processHowToStep($item->getProperties());
            }
        } catch (\Exception $e) {
            Log::error('Failed to process structured data item', [
                'url' => $this->recipeData['url'],
                'item_types' => self::getTypesLower($item),
                'error_message' => $e->getMessage(),
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private function processHowToSection(array $properties): void
    {
        try {
            foreach ($properties as $name => $values) {
                $name = $this->sanitizeName($name);

                if ($name === 'name') {
                    $this->recipeData['steps'][] = '<p><strong>'.html_entity_decode($values[0]).'</strong></p>';
                }

                if ($name === 'itemlistelement') {
                    $this->processItemListElement($values);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process HowToSection from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'properties_count' => count($properties),
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private function processHowToStep(array $properties): void
    {
        try {
            foreach ($properties as $name => $values) {
                $name = $this->sanitizeName($name);

                if ($name === 'text') {
                    $this->recipeData['steps'][] = html_entity_decode($values[0]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process HowToStep from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'properties_count' => count($properties),
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private function processItemListElement(array $values): void
    {
        try {
            $this->recipeData['steps'][] = '<ol>';

            foreach ($values as $value) {
                if ($value instanceof Item) {
                    $this->processItemElement($value->getProperties());
                }
            }

            $this->recipeData['steps'][] = '</ol>';
        } catch (\Exception $e) {
            Log::error('Failed to process ItemListElement from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'values_count' => count($values),
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private function processItemElement(array $properties): void
    {
        try {
            foreach ($properties as $name => $values) {
                $name = $this->sanitizeName($name);

                if ($name === 'text') {
                    $this->recipeData['steps'][] = '<li>'.html_entity_decode($values[0]).'</li>';
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process ItemElement from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'properties_count' => count($properties),
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private function processItemImage(Item $item): void
    {
        try {
            foreach ($item->getProperties() as $name => $values) {
                $name = $this->sanitizeName($name);

                if ($name === 'url' && $this->isRelativeUrl($values[0])) {
                    $this->addImage($values[0]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to process image item from structured data', [
                'url' => $this->recipeData['url'],
                'error_message' => $e->getMessage(),
                'user_id' => optional(auth()->user())->id,
            ]);
        }
    }

    private static function getTypesLower(Item $item): string
    {
        return Str::lower(implode(',', $item->getTypes()));
    }

    private function addImage(string $url): void
    {
        $this->recipeData['images'][] = $url;
    }

    private function isRelativeUrl(string $value): bool
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
}
