<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;
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
            $ingredients = [];
            foreach ($recipe->extendedIngredients as $ingredient) {
                $ingredients[] = round($ingredient->measures->us->amount, 2).' '.$ingredient->measures->us->unitShort.' '.$ingredient->name;
            }

            $model = Recipe::create([
                'user_id' => User::all()->first()->id,
                'title' => $recipe->title,
                'slug' => Str::slug($recipe->title),
                'preparation_minutes' => $recipe->preparationMinutes > -1 ? $recipe->preparationMinutes : 10,
                'cooking_minutes' => $recipe->cookingMinutes > -1 ? $recipe->cookingMinutes : $recipe->readyInMinutes,
                'servings' => $recipe->servings > 0 ? $recipe->servings : 4,
                'difficulty' => array_rand(array_flip(['easy', 'average', 'hard'])),
                'ingredients' => implode("\n", $ingredients),
                'summary' => $recipe->summary,
                'instructions' => $recipe->instructions,
                'source_label' => $recipe->sourceName,
                'source_link' => $recipe->sourceUrl,
            ]);

            if (! empty($recipe->image)) {
                try {
                    $model->addMediaFromUrl($recipe->image)
                        ->toMediaCollection('recipe_image');
                } catch (\Spatie\MediaLibrary\MediaCollections\Exceptions\UnreachableUrl $e) {
                    // Log the error and continue seeding

                }
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
