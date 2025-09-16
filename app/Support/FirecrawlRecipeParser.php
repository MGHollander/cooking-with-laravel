<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirecrawlRecipeParser
{
    public static function read($url)
    {
        $startTime = microtime(true);

        if (empty(config('services.firecrawl.api_key'))) {
            throw new \Exception('Firecrawl API key not set');
        }

        $schema = [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'Recipe title or name',
                ],
                'description' => [
                    'type' => 'string',
                    'description' => 'Recipe description, summary or introduction',
                ],
                'ingredients' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'List of recipe ingredients with quantities and measurements. Each ingredient must include the amount, unit, and ingredient name separated by spaces. Format: "[quantity] [unit] [ingredient name]". Examples: "100 ml milk", "100 g sugar", "1 large egg", "2 cans diced tomatoes", "1 tbsp olive oil". Never concatenate without spaces like "100mlmilk".',
                ],
                'steps' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Step-by-step cooking instructions',
                ],
                'prepTime' => [
                    'type' => 'string',
                    'description' => 'Preparation time in ISO 8601 duration format (PT30M) or minutes as string',
                ],
                'cookTime' => [
                    'type' => 'string',
                    'description' => 'Cooking time in ISO 8601 duration format (PT45M) or minutes as string',
                ],
                'totalTime' => [
                    'type' => 'string',
                    'description' => 'Total time in ISO 8601 duration format (PT1H15M) or minutes as string',
                ],
                'yield' => [
                    'type' => 'number',
                    'description' => 'Number of servings or portions',
                ],
                'difficulty' => [
                    'type' => 'string',
                    'enum' => ['easy', 'average', 'difficult'],
                    'description' => 'Recipe difficulty level',
                ],
                'keywords' => [
                    'type' => 'string',
                    'description' => 'Recipe tags or keywords as comma-separated string',
                ],
                'images' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Recipe image URLs',
                ],
            ],
            'required' => ['title', 'yield', 'ingredients', 'instructions'],
        ];

        try {
            $requestStartTime = microtime(true);

            // Log API request details
            Log::info('Making Firecrawl API request', [
                'url' => $url,
                'api_endpoint' => 'https://api.firecrawl.dev/v2/scrape',
                'timeout' => 120,
                'user_id' => optional(auth()->user())->id,
                'schema_fields' => array_keys($schema['properties']),
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('services.firecrawl.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.firecrawl.dev/v2/scrape', [
                'url' => $url,
                'formats' => [
                    [
                        'type' => 'json',
                        'schema' => $schema,
                    ],
                ],
                'maxAge' => 2629800000000, // 1 month.
            ]);

            $requestDuration = (microtime(true) - $requestStartTime) * 1000; // Convert to milliseconds

            // Log API response details
            $context = [
                'url' => $url,
                'status_code' => $response->status(),
                'response_time_ms' => round($requestDuration, 2),
                'response_json' => $response->json(),
                'user_id' => optional(auth()->user())->id,
            ];

            if ($response->successful()) {
                Log::info('Firecrawl API response received', $context);
            } else {
                Log::error('Firecrawl API request failed', $context);
                throw new \Exception('Firecrawl API request failed: '.$response->body());
            }

            $data = $response->json();

            if (! $data['success'] ?? false) {
                Log::error('Firecrawl API returned unsuccessful response', [
                    'url' => $url,
                    'response_data' => $data,
                    'user_id' => optional(auth()->user())->id,
                ]);
                throw new \Exception('Firecrawl API returned unsuccessful response');
            }

            $recipeData = $data['data']['json'] ?? null;

            if (empty($recipeData) || empty($recipeData['title'])) {
                Log::warning('Firecrawl API returned empty or invalid recipe data', [
                    'url' => $url,
                    'has_data' => ! empty($recipeData),
                    'has_title' => ! empty($recipeData['title'] ?? null),
                    'data' => ! empty($recipeData) ? $recipeData : [],
                    'user_id' => optional(auth()->user())->id,
                ]);

                return null;
            }

            // Add the source URL to the recipe data
            $recipeData['url'] = $url;

            $totalDuration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

            // Log successful recipe parsing with detailed metrics
            Log::info('Recipe successfully parsed from Firecrawl', [
                'url' => $url,
                'recipe_title' => $recipeData['title'] ?? null,
                'extracted_fields' => array_keys($recipeData),
                'total_processing_time_ms' => round($totalDuration, 2),
                'api_response_time_ms' => round($requestDuration, 2),
                'user_id' => optional(auth()->user())->id,
            ]);

            return $recipeData;

        } catch (\Exception $e) {
            $totalDuration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

            // Enhanced error logging with more context
            Log::error('Firecrawl recipe parsing failed', [
                'url' => $url,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'total_processing_time_ms' => round($totalDuration, 2),
                'user_id' => optional(auth()->user())->id,
                'stack_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Return null to indicate parsing failed
            return null;
        }
    }
}
