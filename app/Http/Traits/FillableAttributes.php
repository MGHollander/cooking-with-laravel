<?php

namespace App\Http\Traits;

use App\Models\Recipe;

trait FillableAttributes
{
    // TODO See if this can be refactored to a model trait.
    private function fillableAttributes(Recipe $recipe, array $attributes): array
    {
        return array_filter(
            $attributes,
            static fn($key) => in_array($key, $recipe->getFillable(), true),
            ARRAY_FILTER_USE_KEY
        );
    }
}
