<?php

namespace App\Services\RecipeParsing\Contracts;

use App\Services\RecipeParsing\Exceptions\HtmlFetchException;

interface HtmlFetcherInterface
{
    /**
     * Fetch HTML content from the given URL.
     *
     * @throws HtmlFetchException
     */
    public function fetch(string $url): string;

    /**
     * Fetch HTML content with encoding conversion for structured data parsing.
     *
     * @throws HtmlFetchException
     */
    public function fetchForStructuredData(string $url): string;
}