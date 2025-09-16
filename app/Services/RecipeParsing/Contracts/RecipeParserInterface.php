<?php

namespace App\Services\RecipeParsing\Contracts;

use App\Services\RecipeParsing\Data\ParsedRecipeData;

interface RecipeParserInterface
{
    /**
     * Parse a recipe from the given URL.
     */
    public function parse(string $url): ?ParsedRecipeData;

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