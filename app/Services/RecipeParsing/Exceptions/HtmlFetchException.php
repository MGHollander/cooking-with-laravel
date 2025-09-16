<?php

namespace App\Services\RecipeParsing\Exceptions;

class HtmlFetchException extends RecipeParsingException
{
    public function __construct(string $url, ?string $reason = null, ?\Exception $previous = null)
    {
        parent::__construct(
            message: $reason ? "Failed to fetch HTML from {$url}: {$reason}" : "Failed to fetch HTML from {$url}",
            previous: $previous,
            url: $url,
            parser: 'HtmlFetcher',
            context: ['reason' => $reason]
        );
    }
}