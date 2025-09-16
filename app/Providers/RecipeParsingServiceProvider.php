<?php

namespace App\Providers;

use App\Services\RecipeParsing\Contracts\HtmlFetcherInterface;
use App\Services\RecipeParsing\Services\FirecrawlRecipeParserService;
use App\Services\RecipeParsing\Services\HtmlFetcherService;
use App\Services\RecipeParsing\Services\OpenAIRecipeParserService;
use App\Services\RecipeParsing\Services\RecipeParsingService;
use App\Services\RecipeParsing\Services\StructuredDataRecipeParserService;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;
use OpenAI;

class RecipeParsingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind the HTML fetcher interface
        $this->app->bind(HtmlFetcherInterface::class, HtmlFetcherService::class);

        // Register the HTML fetcher service
        $this->app->singleton(HtmlFetcherService::class, function ($app) {
            return new HtmlFetcherService($app->make(HttpFactory::class));
        });

        // Register individual parser services
        $this->registerFirecrawlParser();
        $this->registerOpenAIParser();
        $this->registerStructuredDataParser();

        // Register the main orchestrating service
        $this->app->singleton(RecipeParsingService::class, function ($app) {
            $service = new RecipeParsingService();

            // Register all available parsers
            $service->registerParser($app->make('recipe.parser.firecrawl'));
            $service->registerParser($app->make('recipe.parser.openai'));
            $service->registerParser($app->make('recipe.parser.structured-data'));

            return $service;
        });
    }

    private function registerFirecrawlParser(): void
    {
        $this->app->singleton('recipe.parser.firecrawl', function ($app) {
            return new FirecrawlRecipeParserService(
                httpClient: $app->make(HttpFactory::class),
                apiKey: config('services.firecrawl.api_key', '')
            );
        });
    }

    private function registerOpenAIParser(): void
    {
        $this->app->singleton('recipe.parser.openai', function ($app) {
            $apiKey = config('services.open_ai.api_key', '');

            // Only create the OpenAI client if we have an API key
            $openAIClient = $apiKey ? OpenAI::client($apiKey) : null;

            return new OpenAIRecipeParserService(
                openAIClient: $openAIClient,
                apiKey: $apiKey
            );
        });
    }

    private function registerStructuredDataParser(): void
    {
        $this->app->singleton('recipe.parser.structured-data', function ($app) {
            return new StructuredDataRecipeParserService(
                htmlFetcher: $app->make(HtmlFetcherInterface::class)
            );
        });
    }

}
