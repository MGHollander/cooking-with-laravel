<?php

namespace App\Services\RecipeParsing\Exceptions;

class ApiKeyMissingException extends RecipeParsingException
{
    public function __construct(string $service, ?string $url = null)
    {
        parent::__construct(
            message: "API key for {$service} is not configured",
            url: $url,
            parser: $service,
            context: ['service' => $service]
        );
    }
}
