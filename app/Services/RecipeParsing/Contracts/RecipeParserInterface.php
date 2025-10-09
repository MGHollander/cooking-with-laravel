<?php

namespace App\Services\RecipeParsing\Contracts;

use App\Services\RecipeParsing\Data\ParserResult;

interface RecipeParserInterface
{
    /**
     * Parse a recipe from the given URL.
     */
    public function parse(string $url): ?ParserResult;

    /**
     * Check if the parser is available (API keys, dependencies, etc.).
     */
    public function isAvailable(): bool;

    /**
     * Get the human-readable name of this parser.
     */
    public function getName(): string;

    /**
     * Get the identifier for this parser.
     */
    public function getIdentifier(): string;
}
