<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'image',
        'servings',
        'preparation_minutes',
        'cooking_minutes',
        'difficulty',
        'summary',
        'ingredients',
        'instructions',
        'source_label',
        'source_link',
    ];

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::of($value)->slug(),
        );
    }

    public function transformIngredients(string $ingredients): array
    {
        $ingredientsArray       = explode("\n", $ingredients);
        $transformedIngredients = [];
        $index                  = 0;

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

    public function parseIngredient($ingredientString)
    {
        $ingredient = ['full' => $ingredientString];

        // Search for an amount as number, fraction or decimal.
        if (preg_match('/^(\d+\/\d+)|(\d+\s\d+\/\d+)|(\d+.\d+)|\d+/', $ingredientString, $matches)) { //Check To See If The Ingredient contains a certain amount.
            $ingredient['amount'] = trim($matches[0]);
            // Remove the amount to extract the unit.
            $ingredientString = trim(str_replace($ingredient['amount'], '', $ingredientString));
        }

        // Search for a unit.
        foreach ($this->getCookingUnits() as $unit) {
            if (preg_match('/^' . $unit . '\.?\s+/i', $ingredientString, $matches)) {
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
            $ingredient['name']        = trim($singularPluralArray[0]);
            $ingredient['name_plural'] = trim($singularPluralArray[1]);
        } else {
            $ingredient['name'] = trim($ingredientString);
        }


        return $ingredient;
    }

    public function getCookingUnits()
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
