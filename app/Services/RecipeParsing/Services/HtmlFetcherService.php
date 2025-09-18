<?php

namespace App\Services\RecipeParsing\Services;

use App\Services\RecipeParsing\Contracts\HtmlFetcherInterface;
use App\Services\RecipeParsing\Exceptions\HtmlFetchException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HtmlFetcherService implements HtmlFetcherInterface
{
    public function __construct(
        private readonly HttpFactory $httpClient
    ) {
    }

    public function fetch(string $url): string
    {
        $startTime = microtime(true);

        try {
            Log::info('Fetching HTML content', [
                'url' => $url,
                'user_id' => Auth::id(),
            ]);

            $response = $this->httpClient
                ->timeout(30)
                ->throw()
                ->get($url);

            $duration = (microtime(true) - $startTime) * 1000;

            Log::info('HTML content fetched successfully', [
                'url' => $url,
                'response_time_ms' => round($duration, 2),
                'content_length' => strlen($response->body()),
                'user_id' => Auth::id(),
            ]);

            return $response->body();

        } catch (\Exception $e) {
            $duration = (microtime(true) - $startTime) * 1000;

            Log::error('Failed to fetch HTML content', [
                'url' => $url,
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'response_time_ms' => round($duration, 2),
                'user_id' => Auth::id(),
            ]);

            throw new HtmlFetchException($url, $e->getMessage(), $e);
        }
    }

    public function fetchForStructuredData(string $url): string
    {
        $html = $this->fetch($url);

        // Convert encoding for structured data parsing as done in the original code
        return mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    }
}
