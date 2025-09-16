<?php

namespace App\Services\RecipeParsing\Data;

class ParsedRecipeData
{
    public function __construct(
        public readonly string $title,
        public readonly array $ingredients,
        public readonly array $steps,
        public readonly ?string $description = null,
        public readonly ?string $keywords = null,
        public readonly ?string $prepTime = null,
        public readonly ?string $cookTime = null,
        public readonly ?string $totalTime = null,
        public readonly ?int $yield = null,
        public readonly ?string $difficulty = null,
        public readonly ?array $images = null,
        public readonly ?string $url = null,
    ) {
    }

    /**
     * Convert to array format for compatibility with existing ImportResource.
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'ingredients' => $this->ingredients,
            'steps' => $this->steps,
            'prepTime' => $this->prepTime,
            'cookTime' => $this->cookTime,
            'totalTime' => $this->totalTime,
            'yield' => $this->yield,
            'difficulty' => $this->difficulty,
            'images' => $this->images,
            'url' => $this->url,
        ];
    }

    /**
     * Create from array data (for backward compatibility).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            ingredients: $data['ingredients'] ?? [],
            steps: $data['steps'] ?? [],
            description: $data['description'] ?? null,
            keywords: $data['keywords'] ?? null,
            prepTime: $data['prepTime'] ?? null,
            cookTime: $data['cookTime'] ?? null,
            totalTime: $data['totalTime'] ?? null,
            yield: isset($data['yield']) ? (int) $data['yield'] : null,
            difficulty: $data['difficulty'] ?? null,
            images: $data['images'] ?? null,
            url: $data['url'] ?? null,
        );
    }

    /**
     * Check if the recipe data is valid (has required fields).
     */
    public function isValid(): bool
    {
        return !empty($this->title) 
            && !empty($this->ingredients) 
            && !empty($this->steps);
    }
}