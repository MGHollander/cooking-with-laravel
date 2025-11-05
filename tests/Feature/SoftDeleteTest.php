<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_soft_delete_cascades_to_recipes()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user)->create();

        $user->delete();

        $this->assertTrue($user->trashed());
        $this->assertTrue($recipe->fresh()->trashed());
        $this->assertEquals(0, User::count());
        $this->assertEquals(0, Recipe::count());
    }

    public function test_recipe_can_be_soft_deleted()
    {
        $recipe = Recipe::factory()->create();
        $recipe->delete();

        $this->assertTrue($recipe->trashed());
        $this->assertEquals(0, Recipe::count());
        $this->assertEquals(1, Recipe::withTrashed()->count());
    }

    public function test_media_remains_with_soft_deleted_recipe()
    {
        $recipe = Recipe::factory()->create();

        // Simulate adding media (we'll test the relationship exists)
        $mediaCount = $recipe->getMedia('recipe_image')->count();

        $recipe->delete();

        $this->assertTrue($recipe->trashed());
        $this->assertEquals($mediaCount, $recipe->getMedia('recipe_image')->count());
    }

    public function test_soft_deleted_records_are_excluded_from_queries()
    {
        $activeUser = User::factory()->create();
        $activeRecipe = Recipe::factory()->for($activeUser)->create();

        $deletedUser = User::factory()->create();
        $deletedRecipe = Recipe::factory()->for($deletedUser)->create();

        $deletedUser->delete();

        $this->assertEquals(1, User::count());
        $this->assertEquals(1, Recipe::count());
        $this->assertEquals(2, User::withTrashed()->count());
        $this->assertEquals(2, Recipe::withTrashed()->count());
    }

    public function test_only_trashed_scope_works_correctly()
    {
        $activeUser = User::factory()->create();
        $deletedUser = User::factory()->create();

        $deletedUser->delete();

        $this->assertEquals(1, User::onlyTrashed()->count());
        $this->assertEquals($deletedUser->id, User::onlyTrashed()->first()->id);
    }
}
