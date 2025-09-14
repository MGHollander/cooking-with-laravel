<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

class FirecrawlRecipeParser
{
    public static function read($url)
    {
        if (empty(config('services.firecrawl.api_key'))) {
            throw new \Exception('Firecrawl API key not set');
        }

        $schema = [
            'type' => 'object',
            'properties' => [
                'title' => [
                    'type' => 'string',
                    'description' => 'Recipe title or name'
                ],
                'description' => [
                    'type' => 'string',
                    'description' => 'Recipe description, summary or introduction'
                ],
                'ingredients' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'List of recipe ingredients with quantities'
                ],
                'steps' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Step-by-step cooking instructions'
                ],
                'prepTime' => [
                    'type' => 'string',
                    'description' => 'Preparation time in ISO 8601 duration format (PT30M) or minutes as string'
                ],
                'cookTime' => [
                    'type' => 'string',
                    'description' => 'Cooking time in ISO 8601 duration format (PT45M) or minutes as string'
                ],
                'totalTime' => [
                    'type' => 'string',
                    'description' => 'Total time in ISO 8601 duration format (PT1H15M) or minutes as string'
                ],
                'yield' => [
                    'type' => 'number',
                    'description' => 'Number of servings or portions'
                ],
                'difficulty' => [
                    'type' => 'string',
                    'enum' => ['easy', 'average', 'difficult'],
                    'description' => 'Recipe difficulty level'
                ],
                'keywords' => [
                    'type' => 'string',
                    'description' => 'Recipe tags or keywords as comma-separated string'
                ],
                'images' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Recipe image URLs'
                ]
            ],
            'required' => ['title', 'yield', 'ingredients', 'instructions']
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.firecrawl.api_key'),
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.firecrawl.dev/v2/scrape', [
                'url' => $url,
                'formats' => [
                    [
                        'type' => 'json',
                        'schema' => $schema
                    ]
                ],
                'maxAge' => 2629800000000, // 1 month.
            ]);

            if (!$response->successful()) {
                throw new \Exception('Firecrawl API request failed: ' . $response->body());
            }

            $data = $response->json();

            if (!$data['success'] ?? false) {
                throw new \Exception('Firecrawl API returned unsuccessful response');
            }

            $recipeData = $data['data']['json'] ?? null;

            if (empty($recipeData) || empty($recipeData['title'])) {
                return null;
            }

            // Add the source URL to the recipe data
            $recipeData['url'] = $url;

            return $recipeData;

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Firecrawl API error: ' . $e->getMessage(), [
                'url' => $url,
                'trace' => $e->getTraceAsString()
            ]);

            // Return null to indicate parsing failed
            return null;
        }
    }
}
