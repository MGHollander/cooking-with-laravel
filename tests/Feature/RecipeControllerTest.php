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

        $response = $this->get(route('recipes.show.en', [$recipe->public_id, 'my-recipe']));

        $response->assertStatus(200);
        $response->assertSee('My Recipe');
    }

    public function test_legacy_recipe_url_without_public_id_redirects()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'My Recipe',
            'slug' => 'my-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response = $this->get(route('recipes.show.legacy.en', 'my-recipe'));

        $response->assertRedirect(route('recipes.show.en', [$recipe->public_id, 'my-recipe']));
        $response->assertStatus(301);
    }

    public function test_self_healing_url_redirects_to_correct_slug()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'My Recipe',
            'slug' => 'my-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        // Access with wrong slug
        $response = $this->get(route('recipes.show.en', [$recipe->public_id, 'wrong-slug']));
        $response->assertRedirect(route('recipes.show.en', [$recipe->public_id, 'my-recipe']));
        $response->assertStatus(301);

        // Access with no slug (self-healing)
        $response = $this->get(route('recipes.show.en', [$recipe->public_id]));
        $response->assertRedirect(route('recipes.show.en', [$recipe->public_id, 'my-recipe']));
        $response->assertStatus(301);
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
