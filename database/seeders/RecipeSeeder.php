<?php

namespace Database\Seeders;

use App\Models\Recipe;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // @TODO Add real recipes from an API.
        for ($i = 0; $i < 50; $i++) {
            Recipe::factory()
                ->hasIngredients(mt_rand(5, 15))
                ->create();
        }
    }
}
