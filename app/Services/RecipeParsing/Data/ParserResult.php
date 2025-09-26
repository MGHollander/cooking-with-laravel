<?php

namespace App\Services\RecipeParsing\Data;

class ParserResult
{
    public function __construct(
        public readonly ?ParsedRecipeData $recipe,
        public readonly ?int $creditsUsed = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            recipe: isset($data['recipe']) ? ParsedRecipeData::fromArray($data['recipe']) : null,
            creditsUsed: isset($data['creditsUsed']) ? (int) $data['creditsUsed'] : 0
        );
    }

    public function toArray(): array
    {
        return [
            'recipe' => $this->recipe?->toArray(),
            'creditsUsed' => $this->creditsUsed,
        ];
    }

    public function isValid(): bool
    {
        return $this->recipe !== null && $this->recipe->isValid();
    }
}
