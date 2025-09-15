<?php

namespace Tests\Unit;

use App\Support\FirecrawlRecipeParser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FirecrawlRecipeParserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set up Firecrawl API key for testing
        Config::set('services.firecrawl.api_key', 'test-api-key');
    }

    public function test_logs_successful_recipe_parsing(): void
    {
        // Spy on the log to capture log entries
        Log::spy();

        // Mock successful Firecrawl API response
        Http::fake([
            'api.firecrawl.dev/v2/scrape' => Http::response([
                'success' => true,
                'data' => [
                    'json' => [
                        'title' => 'Test Recipe',
                        'description' => 'A delicious test recipe',
                        'ingredients' => ['1 cup flour', '2 eggs'],
                        'steps' => ['Mix ingredients', 'Bake for 30 minutes'],
                        'yield' => 4,
                        'difficulty' => 'easy',
                        'prepTime' => 'PT15M',
                        'cookTime' => 'PT30M',
                        'totalTime' => 'PT45M',
                        'keywords' => 'test, recipe',
                        'images' => ['https://example.com/image.jpg'],
                    ],
                ],
            ], 200),
        ]);

        $url = 'https://example.com/recipe';
        $result = FirecrawlRecipeParser::read($url);

        // Assert the recipe data was returned correctly
        $this->assertNotNull($result);
        $this->assertEquals('Test Recipe', $result['title']);
        $this->assertEquals($url, $result['url']);

        // Assert that appropriate log entries were created
        Log::shouldHaveReceived('info')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Firecrawl recipe parsing started'
                    && $context['url'] === $url;
            });

        Log::shouldHaveReceived('info')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Making Firecrawl API request'
                    && $context['url'] === $url
                    && $context['api_endpoint'] === 'https://api.firecrawl.dev/v2/scrape';
            });

        Log::shouldHaveReceived('info')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Firecrawl API response received'
                    && $context['url'] === $url
                    && $context['status_code'] === 200;
            });

        Log::shouldHaveReceived('info')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Recipe successfully parsed from Firecrawl'
                    && $context['url'] === $url
                    && $context['recipe_title'] === 'Test Recipe'
                    && $context['ingredients_count'] === 2
                    && $context['steps_count'] === 2
                    && $context['has_images'] === true
                    && $context['images_count'] === 1
                    && isset($context['total_processing_time_ms']);
            });
    }

    public function test_logs_api_failure(): void
    {
        Log::spy();

        // Mock failed Firecrawl API response
        Http::fake([
            'api.firecrawl.dev/v2/scrape' => Http::response([
                'error' => 'API limit exceeded',
            ], 429),
        ]);

        $url = 'https://example.com/recipe';
        $result = FirecrawlRecipeParser::read($url);

        // Assert null was returned for failed parsing
        $this->assertNull($result);

        // Assert error logging occurred
        Log::shouldHaveReceived('error')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Firecrawl API request failed'
                    && $context['url'] === $url
                    && $context['status_code'] === 429;
            });

        Log::shouldHaveReceived('error')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Firecrawl recipe parsing failed'
                    && $context['url'] === $url
                    && str_contains($context['error_message'], 'Firecrawl API request failed');
            });
    }

    public function test_logs_empty_recipe_data(): void
    {
        Log::spy();

        // Mock API response with empty recipe data
        Http::fake([
            'api.firecrawl.dev/v2/scrape' => Http::response([
                'success' => true,
                'data' => [
                    'json' => [],
                ],
            ], 200),
        ]);

        $url = 'https://example.com/recipe';
        $result = FirecrawlRecipeParser::read($url);

        // Assert null was returned for empty data
        $this->assertNull($result);

        // Assert warning was logged (empty array is considered empty, so has_data should be false)
        Log::shouldHaveReceived('warning')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Firecrawl API returned empty or invalid recipe data'
                    && $context['url'] === $url
                    && $context['has_data'] === false
                    && $context['has_title'] === false;
            });
    }

    public function test_logs_missing_api_key(): void
    {
        Log::spy();

        // Remove the API key
        Config::set('services.firecrawl.api_key', null);

        $url = 'https://example.com/recipe';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Firecrawl API key not set');

        FirecrawlRecipeParser::read($url);

        // Assert error was logged for missing API key
        Log::shouldHaveReceived('error')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Firecrawl API key not configured'
                    && $context['url'] === $url;
            });
    }

    public function test_logs_unsuccessful_api_response(): void
    {
        Log::spy();

        // Mock API response with success=false
        Http::fake([
            'api.firecrawl.dev/v2/scrape' => Http::response([
                'success' => false,
                'error' => 'Failed to parse website',
            ], 200),
        ]);

        $url = 'https://example.com/recipe';
        $result = FirecrawlRecipeParser::read($url);

        // Assert null was returned
        $this->assertNull($result);

        // Assert error was logged
        Log::shouldHaveReceived('error')
            ->withArgs(function ($message, $context) use ($url) {
                return $message === 'Firecrawl API returned unsuccessful response'
                    && $context['url'] === $url
                    && isset($context['response_data']);
            });
    }

    public function test_includes_performance_metrics_in_logs(): void
    {
        Log::spy();

        Http::fake([
            'api.firecrawl.dev/v2/scrape' => Http::response([
                'success' => true,
                'data' => [
                    'json' => [
                        'title' => 'Performance Test Recipe',
                        'yield' => 2,
                        'ingredients' => ['ingredient1'],
                        'steps' => ['step1'],
                    ],
                ],
            ], 200),
        ]);

        $url = 'https://example.com/recipe';
        FirecrawlRecipeParser::read($url);

        // Assert performance metrics are logged
        Log::shouldHaveReceived('info')
            ->withArgs(function ($message, $context) {
                return $message === 'Firecrawl API response received'
                    && isset($context['response_time_ms'])
                    && isset($context['response_size_bytes']);
            });

        Log::shouldHaveReceived('info')
            ->withArgs(function ($message, $context) {
                return $message === 'Recipe successfully parsed from Firecrawl'
                    && isset($context['total_processing_time_ms'])
                    && isset($context['api_response_time_ms']);
            });
    }
}
