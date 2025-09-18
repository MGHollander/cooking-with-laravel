<?php

namespace App\Services\RecipeParsing\Services;

use App\Services\RecipeParsing\Contracts\RecipeParserInterface;
use App\Services\RecipeParsing\Data\ParsedRecipeData;
use App\Services\RecipeParsing\Exceptions\ApiKeyMissingException;
use App\Services\RecipeParsing\Exceptions\RecipeParsingException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FirecrawlRecipeParserService implements RecipeParserInterface
{
    private const API_ENDPOINT = 'https://api.firecrawl.dev/v2/scrape';

    private const TIMEOUT = 120;

    private const MAX_AGE = 2629800000000; // 1 month

    public function __construct(
        private readonly HttpFactory $httpClient,
        private readonly string $apiKey
    ) {}

    public function parse(string $url): ?ParsedRecipeData
    {
        if (! $this->isAvailable()) {
            throw new ApiKeyMissingException('Firecrawl', $url);
        }

        $startTime = microtime(true);

        try {
            // First attempt with basic proxy
            Log::info('Starting Firecrawl parsing with basic proxy', [
                'url' => $url,
                'user_id' => Auth::id(),
            ]);

            $result = $this->makeRequest($url, 'basic');

            if ($result['success']) {
                return $this->processSuccessfulResponse($result['data'], $result['requestDuration'], $startTime, $url);
            }

            // Check if we should retry with stealth proxy
            $shouldRetry = $this->shouldRetryWithStealth($result['response'], $result['data']);

            if ($shouldRetry) {
                Log::info('Firecrawl basic proxy failed, retrying with stealth proxy', [
                    'url' => $url,
                    'reason' => $result['failureReason'],
                    'user_id' => Auth::id(),
                ]);

                $retryResult = $this->makeRequest($url, 'stealth');

                if ($retryResult['success']) {
                    Log::info('Firecrawl stealth proxy retry successful', [
                        'url' => $url,
                        'user_id' => Auth::id(),
                    ]);

                    return $this->processSuccessfulResponse($retryResult['data'], $retryResult['requestDuration'], $startTime, $url);
                }

                Log::warning('Firecrawl stealth proxy retry also failed', [
                    'url' => $url,
                    'failure_reason' => $retryResult['failureReason'],
                    'user_id' => Auth::id(),
                ]);

                // Use the retry result for error handling
                $result = $retryResult;
            }

            // Handle failure
            $this->handleRequestFailure($result['response'], $result['data'], $url, $result['requestDuration']);

            return null; // Unreachable, but satisfies linter

        } catch (RecipeParsingException $e) {
            throw $e;
        } catch (\Exception $e) {
            $totalDuration = (microtime(true) - $startTime) * 1000;

            Log::error('Firecrawl recipe parsing failed', [
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
                message: 'Firecrawl parsing failed: '.$e->getMessage(),
                previous: $e,
                url: $url,
                parser: $this->getName()
            );
        }
    }

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey);
    }

    public function getName(): string
    {
        return 'Firecrawl';
    }

    public function getIdentifier(): string
    {
        return 'firecrawl';
    }

    private function getRecipeSchema(): array
    {
        return [
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
            'required' => ['title', 'ingredients', 'steps'],
        ];
    }

    private function makeRequest(string $url, string $proxy): array
    {
        $requestStartTime = microtime(true);

        Log::info('Making Firecrawl API request', [
            'url' => $url,
            'proxy' => $proxy,
            'schema_fields' => array_keys($this->getRecipeSchema()['properties']),
            'user_id' => Auth::id(),
        ]);

        $response = $this->httpClient
            ->withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(self::TIMEOUT)
            ->post(self::API_ENDPOINT, [
                'url' => $url,
                'formats' => [
                    [
                        'type' => 'json',
                        'schema' => $this->getRecipeSchema(),
                    ],
                ],
                'maxAge' => self::MAX_AGE,
                'proxy' => $proxy,
            ]);

        $requestDuration = (microtime(true) - $requestStartTime) * 1000;

        $context = [
            'url' => $url,
            'proxy' => $proxy,
            'status_code' => $response->status(),
            'response_time_ms' => round($requestDuration, 2),
            'response_json' => $response->json(),
            'user_id' => Auth::id(),
        ];

        if ($response->successful()) {
            Log::info('Firecrawl API response received', $context);
        } else {
            Log::warning('Firecrawl API request failed', $context);
        }

        $data = $response->json();

        return [
            'response' => $response,
            'data' => $data,
            'requestDuration' => $requestDuration,
            'success' => $response->successful() && ($data['success'] ?? false),
            'failureReason' => $this->getFailureReason($response, $data),
        ];
    }

    private function shouldRetryWithStealth($response, $data): bool
    {
        $statusCode = $response->status();

        // Retry on blocking status codes
        if (in_array($statusCode, [401, 403, 500])) {
            return true;
        }

        // Check metadata status code if available
        $metadataStatusCode = $data['metadata']['statusCode'] ?? null;
        if ($metadataStatusCode && in_array($metadataStatusCode, [401, 403, 500])) {
            return true;
        }

        return false;
    }

    private function getFailureReason($response, $data): string
    {
        $statusCode = $response->status();
        $metadataStatusCode = $data['metadata']['statusCode'] ?? null;

        if (in_array($statusCode, [401, 403, 500])) {
            return "HTTP {$statusCode}";
        }

        if ($metadataStatusCode && in_array($metadataStatusCode, [401, 403, 500])) {
            return "Metadata status {$metadataStatusCode}";
        }

        if (! $response->successful()) {
            return "HTTP {$statusCode}: ".$response->body();
        }

        if (! ($data['success'] ?? false)) {
            return 'API returned unsuccessful response';
        }

        return 'Unknown failure';
    }

    private function processSuccessfulResponse(array $data, float $requestDuration, float $startTime, string $url): ?ParsedRecipeData
    {
        $recipeData = $data['data']['json'] ?? null;

        if (empty($recipeData)) {
            Log::warning('Firecrawl API returned empty recipe data', [
                'url' => $url,
                'data' => $data,
                'user_id' => Auth::id(),
            ]);

            return null;
        }

        $parsedData = ParsedRecipeData::fromArray(array_merge($recipeData, ['url' => $url]));

        if (! $parsedData->isValid()) {
            $missingFields = [];
            if (empty($parsedData->title)) {
                $missingFields[] = 'title';
            }
            if (empty($parsedData->ingredients)) {
                $missingFields[] = 'ingredients';
            }
            if (empty($parsedData->steps)) {
                $missingFields[] = 'steps';
            }

            Log::warning('Firecrawl API returned invalid recipe data', [
                'url' => $url,
                'missing_fields' => $missingFields,
                'has_title' => ! empty($parsedData->title),
                'has_ingredients' => ! empty($parsedData->ingredients),
                'has_steps' => ! empty($parsedData->steps),
                'user_id' => Auth::id(),
            ]);

            return null;
        }

        $totalDuration = (microtime(true) - $startTime) * 1000;

        Log::info('Recipe successfully parsed from Firecrawl', [
            'url' => $url,
            'recipe_title' => $parsedData->title,
            'extracted_fields' => array_keys(array_filter($parsedData->toArray())),
            'total_processing_time_ms' => round($totalDuration, 2),
            'api_response_time_ms' => round($requestDuration, 2),
            'user_id' => Auth::id(),
        ]);

        return $parsedData;
    }

    private function handleRequestFailure($response, $data, string $url, float $requestDuration): never
    {
        $context = [
            'url' => $url,
            'status_code' => $response->status(),
            'response_time_ms' => round($requestDuration, 2),
            'response_json' => $response->json(),
            'user_id' => Auth::id(),
        ];

        Log::error('Firecrawl API request failed', $context);

        if (! ($data['success'] ?? false)) {
            Log::error('Firecrawl API returned unsuccessful response', [
                'url' => $url,
                'response_data' => $data,
                'user_id' => Auth::id(),
            ]);
        }

        throw new RecipeParsingException(
            message: 'Firecrawl API request failed: '.$response->body(),
            url: $url,
            parser: $this->getName()
        );
    }
}
