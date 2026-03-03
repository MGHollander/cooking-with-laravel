<?php

namespace Tests\Feature;

use App\Enums\RecipeVisibility;
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

    public function test_edit_recipe_page_loads_for_authenticated_user()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
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

        $response = $this->actingAs($user)
            ->get(route('recipes.edit.en', $recipe));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Recipes/Form')
            ->has('recipe', fn ($page) => $page
                ->where('id', $recipe->id)
                ->where('title', 'My Recipe')
                ->etc()
            )
        );
    }

    public function test_edit_recipe_page_redirects_unauthenticated_user_to_login()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get(route('recipes.edit.nl', $recipe));

        $response->assertRedirect(route('login.nl'));
    }

    public function test_recipe_can_be_created_with_visibility_private()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('recipes.store.en'), [
            'locale' => 'en',
            'title' => 'Test Recipe',
            'servings' => 4,
            'difficulty' => 'easy',
            'ingredients' => '[]',
            'instructions' => '[]',
            'visibility' => RecipeVisibility::Private->value,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('recipes', [
            'user_id' => $user->id,
            'visibility' => 'private',
        ]);
    }

    public function test_recipe_can_be_created_with_visibility_public()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('recipes.store.en'), [
            'locale' => 'en',
            'title' => 'Test Recipe Public',
            'servings' => 4,
            'difficulty' => 'easy',
            'ingredients' => '[]',
            'instructions' => '[]',
            'visibility' => RecipeVisibility::Public->value,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('recipes', [
            'user_id' => $user->id,
            'visibility' => RecipeVisibility::Public->value,
        ]);
    }

    public function test_recipe_defaults_to_private_visibility()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('recipes.store.en'), [
            'locale' => 'en',
            'title' => 'Test Recipe Default',
            'servings' => 4,
            'difficulty' => 'easy',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('recipes', [
            'user_id' => $user->id,
            'visibility' => 'private',
        ]);
    }

    public function test_recipe_visibility_can_be_updated()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'private',
        ]);

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'My Recipe',
            'slug' => 'my-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response = $this->actingAs($user)->patch(route('recipes.update.en', $recipe), [
            'locale' => 'en',
            'title' => 'Updated Recipe',
            'servings' => 4,
            'difficulty' => 'easy',
            'ingredients' => '[]',
            'instructions' => '[]',
            'visibility' => RecipeVisibility::Public->value,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'visibility' => RecipeVisibility::Public->value,
        ]);
    }

    public function test_recipe_visibility_can_be_set_to_direct_link()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'visibility' => 'private',
        ]);

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'My Recipe',
            'slug' => 'my-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response = $this->actingAs($user)->patch(route('recipes.update.en', $recipe), [
            'locale' => 'en',
            'title' => 'Updated Recipe',
            'servings' => 4,
            'difficulty' => 'easy',
            'ingredients' => '[]',
            'instructions' => '[]',
            'visibility' => RecipeVisibility::DirectLink->value,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'visibility' => RecipeVisibility::DirectLink->value,
        ]);
    }
}
