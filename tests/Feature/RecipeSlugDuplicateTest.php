<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeSlugDuplicateTest extends TestCase
{
    use RefreshDatabase;

    public function test_multiple_recipes_can_have_the_same_slug()
    {
        $user = User::factory()->create();

        $recipe1 = Recipe::factory()->create(['user_id' => $user->id]);
        $recipe1->translations()->create([
            'locale' => 'en',
            'title' => 'Duplicate Title',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $recipe2 = Recipe::factory()->create(['user_id' => $user->id]);
        $recipe2->translations()->create([
            'locale' => 'en',
            'title' => 'Duplicate Title',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $recipe1->refresh();
        $recipe2->refresh();

        $slug1 = $recipe1->translate('en')->slug;
        $slug2 = $recipe2->translate('en')->slug;

        $this->assertEquals('duplicate-title', $slug1);
        $this->assertEquals('duplicate-title', $slug2);
        $this->assertNotEquals($recipe1->id, $recipe2->id);

        // Verify routing works for both even with same slug
        $response1 = $this->get(route('recipes.show.en', [$recipe1->public_id, $slug1]));
        $response1->assertStatus(200);
        $response1->assertSee($recipe1->translate('en')->title);

        $response2 = $this->get(route('recipes.show.en', [$recipe2->public_id, $slug2]));
        $response2->assertStatus(200);
        $response2->assertSee($recipe2->translate('en')->title);
    }

    public function test_self_healing_still_works_with_duplicate_slugs()
    {
        $user = User::factory()->create();

        $recipe1 = Recipe::factory()->create(['user_id' => $user->id]);
        $recipe1->translations()->create([
            'locale' => 'en',
            'title' => 'Duplicate Title',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $recipe2 = Recipe::factory()->create(['user_id' => $user->id]);
        $recipe2->translations()->create([
            'locale' => 'en',
            'title' => 'Duplicate Title',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $recipe1->refresh();
        $recipe2->refresh();

        $slug1 = $recipe1->translate('en')->slug;
        $slug2 = $recipe2->translate('en')->slug;

        // Access recipe1 with WRONG slug, should redirect to its canonical slug
        $response = $this->get(route('recipes.show.en', [$recipe1->public_id, 'wrong-slug']));
        $response->assertRedirect(route('recipes.show.en', [$recipe1->public_id, $slug1]));
        $response->assertStatus(301);

        // Access recipe2 with WRONG slug, should redirect to its canonical slug
        $response = $this->get(route('recipes.show.en', [$recipe2->public_id, 'wrong-slug']));
        $response->assertRedirect(route('recipes.show.en', [$recipe2->public_id, $slug2]));
        $response->assertStatus(301);
    }
}
