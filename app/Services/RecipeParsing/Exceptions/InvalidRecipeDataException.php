<?php

namespace App\Services\RecipeParsing\Exceptions;

class InvalidRecipeDataException extends RecipeParsingException
{
    public function __construct(string $url, array $missingFields = [], ?string $parser = null)
    {
        $message = 'Invalid recipe data';
        if (! empty($missingFields)) {
            $message .= ': missing required fields - '.implode(', ', $missingFields);
        }

        parent::__construct(
            message: $message,
            url: $url,
            parser: $parser,
            context: ['missing_fields' => $missingFields]
        );
    }
}
