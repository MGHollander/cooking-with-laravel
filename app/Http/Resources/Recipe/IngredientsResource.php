<?php

namespace App\Http\Resources\Recipe;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class IngredientsResource extends JsonResource
{
    public static $wrap;

    public function toArray($request): array
    {
        return $this->transformIngredients($this->resource);
    }

    public function transformIngredients(string $ingredients): array
    {
        $ingredientsArray = explode("\n", $ingredients);
        $transformedIngredients = [];
        $index = 0;

        foreach ($ingredientsArray as $line) {
            if (Str::startsWith($line, '#')) {
                $index++;
                $transformedIngredients[$index]['title'] = trim(Str::after($line, '#'));

                continue;
            }

            if (empty(trim($line))) {
                $index++;

                continue;
            }

            $transformedIngredients[$index]['ingredients'][] = $this->parseIngredient($line);
        }

        return $transformedIngredients;
    }

    public function parseIngredient(string $ingredientString): array
    {
        $ingredient = ['full' => $ingredientString];

        // Search for an amount as number, fraction or decimal.
        if (preg_match('/^(\d+\/\d+)|(\d+\s\d+\/\d+)|(\d+.\d+)|\d+/', $ingredientString, $matches)) { // Check To See If The Ingredient contains a certain amount.
            $ingredient['amount'] = trim($matches[0]);
            // Remove the amount to extract the unit.
            $ingredientString = trim(str_replace($ingredient['amount'], '', $ingredientString));
        }

        // Search for a unit.
        foreach ($this->getCookingUnits() as $unit) {
            if (preg_match('/^'.$unit.'\.?\s+/i', $ingredientString, $matches)) {
                $ingredient['unit'] = strtolower(trim(str_replace('.', '', $matches[0])));
                // Remove the unit and amount to extract the name.
                $ingredientString = trim(str_replace($matches[0], '', $ingredientString));
                break;
            }
        }

        // Search for an info in parantheses.
        if (preg_match('/( ?\((.*?)\))/', $ingredientString, $matches)) {
            $ingredient['info'] = $matches[0];
            // Remove the info to extract the name.
            $ingredientString = trim(str_replace($ingredient['info'], '', $ingredientString));
        }

        // The name is what's left :-).
        $singularPluralArray = explode('|', $ingredientString);
        if (count($singularPluralArray) > 1) {
            $ingredient['name'] = trim($singularPluralArray[0]);
            $ingredient['name_plural'] = trim($singularPluralArray[1]);
        } else {
            $ingredient['name'] = trim($ingredientString);
        }

        return $ingredient;
    }

    public function getCookingUnits(): array
    {
        return [
            'teaspoon',
            'tsp',
            'tsps',

            'theelepel',
            'tl',

            'ounce',
            'oz',

            'gram',
            'gr',

            'pounds',
            'lb',
            'lbs',

            'tablespoon',
            'tbl',
            'tbs',
            'tbsp',
            'tbsps',

            'eetlepel',
            'el',

            'fluid ounce',
            'fl oz',

            'gill',

            'cup',
            'cups',
            'c',

            'pint',
            'p',
            'pt',
            'fl pt',

            'quart',
            'q',
            'qt',
            'fl',
            'qt',

            'gallon',
            'gal',

            'milliliter',
            'millilitre',
            'ml',
            'cc',

            'liter',
            'litre',
            'l',
        ];
    }
}
