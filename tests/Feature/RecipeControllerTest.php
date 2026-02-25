<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_show_page_loads_with_correct_slug()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'My Recipe',
            'slug' => 'my-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $fullSlug = 'my-recipe-'.$recipe->public_id;

        $response = $this->get(route('recipes.show.en', $fullSlug));

        $response->assertStatus(200);
        $response->assertSee('My Recipe');
    }

    public function test_recipe_index_page_loads()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'My Recipe',
            'slug' => 'my-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response = $this->get(route('home')); // Assuming home is index

        $response->assertStatus(200);
        $response->assertSee('My Recipe');
    }
}
