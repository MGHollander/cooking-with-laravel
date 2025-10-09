<?php

namespace App\Services\RecipeParsing\Exceptions;

use Exception;
use Illuminate\Support\Facades\Auth;

class RecipeParsingException extends Exception
{
    public function __construct(
        string $message = 'Recipe parsing failed',
        int $code = 0,
        ?Exception $previous = null,
        public readonly ?string $url = null,
        public readonly ?string $parser = null,
        public readonly ?array $context = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get context data for logging.
     */
    public function getContext(): array
    {
        return array_filter([
            'url' => $this->url,
            'parser' => $this->parser,
            'error_message' => $this->getMessage(),
            'error_code' => $this->getCode(),
            'context' => $this->context,
            'user_id' => Auth::id(),
        ]);
    }
}
