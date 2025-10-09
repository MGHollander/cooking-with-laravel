<?php

namespace App\Http\Resources\StructuredData\Recipe;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IngredientsResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ingredients = [];

        foreach (explode("\n", $this->resource) as $line) {
            if (empty(trim($line)) || Str::startsWith($line, '#')) {
                continue;
            }

            $ingredients[] = $line;
        }

        return $ingredients;
    }
}
