<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\IngredientsList;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getRecipes() as $recipe) {
            $slug = Str::slug($recipe->title);
            $image = null;

            if (!empty($recipe->image)) {
                $contents = file_get_contents($recipe->image);
                $extension = substr($recipe->image, strrpos($recipe->image, '.') + 1);
                $image = 'public/images/' . $slug . '.' . $extension;
                Storage::put($image, $contents);
            }

            $recipeModel = Recipe::create([
                'user_id' => User::all()->first()->id,
                'title' => $recipe->title,
                'slug' => $slug,
                'image' => $image,
                'preparation_minutes' => $recipe->preparationMinutes > -1 ? $recipe->preparationMinutes : 10,
                'cooking_minutes' => $recipe->cookingMinutes > -1 ? $recipe->cookingMinutes : $recipe->readyInMinutes,
                'servings' => $recipe->servings > 0 ? $recipe->servings : 4,
                'difficulty' => array_rand(array_flip(['Easy', 'Moderate', 'Hard'])),
                'summary' => $recipe->summary,
                'instructions' => $recipe->instructions,
                'source_label' => $recipe->sourceName,
                'source_link' => $recipe->sourceUrl,
            ]);

            $ingredientsListModel = IngredientsList::create([
                'recipe_id' => $recipeModel->id,
            ]);

            foreach ($recipe->extendedIngredients as $ingredient) {
                Ingredient::create([
                    'ingredients_list_id' => $ingredientsListModel->id,
                    'name' => $ingredient->name,
                    'amount' => round($ingredient->measures->us->amount, 2),
                    'unit' => $ingredient->measures->us->unitShort,
                ]);
            }
        }
    }

    private function getRecipes()
    {
        $recipes = file_get_contents(resource_path('json/recipes.json'));
        $recipes = json_decode($recipes);

        return $recipes->recipes;
    }
}
