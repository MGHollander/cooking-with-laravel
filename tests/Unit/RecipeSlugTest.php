<?php

namespace Tests\Unit;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_slug_accessor_returns_full_slug()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
            'public_id' => '12345',
        ]);

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'My Recipe',
            'slug' => 'my-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        // Set locale to en
        app()->setLocale('en');

        // Refresh to load translations
        $recipe->refresh();

        $this->assertEquals('my-recipe', $recipe->getSlugForLocale('en'));
    }
}
