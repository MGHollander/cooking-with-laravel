<?php

namespace Tests\Feature;

use App\Models\ImportLog;
use App\Models\Recipe;
use App\Models\RecipeTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UuidPrimaryKeysTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_have_uuid_as_primary_key()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->id);
        $this->assertTrue($this->isValidUuid($user->id));
    }

    public function test_recipes_have_uuid_as_primary_key()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $this->assertNotNull($recipe->id);
        $this->assertTrue($this->isValidUuid($recipe->id));
    }

    public function test_recipe_translations_have_uuid_as_primary_key()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $translation = $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $this->assertNotNull($translation->id);
        $this->assertTrue($this->isValidUuid($translation->id));
    }

    public function test_import_logs_have_uuid_as_primary_key()
    {
        $user = User::factory()->create();

        $importLog = ImportLog::factory()->for($user)->create();

        $this->assertNotNull($importLog->id);
        $this->assertTrue($this->isValidUuid($importLog->id));
    }

    public function test_recipe_user_relationship_works_with_uuid()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $this->assertEquals($user->id, $recipe->user_id);
        $this->assertEquals($user->id, $recipe->author->id);
    }

    public function test_recipe_translation_relationship_works_with_uuid()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $translation = $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $this->assertEquals($recipe->id, $translation->recipe_id);
        $this->assertEquals($recipe->id, $translation->recipe->id);
    }

    public function test_import_log_user_relationship_works_with_uuid()
    {
        $user = User::factory()->create();
        $importLog = ImportLog::factory()->for($user)->create();

        $this->assertEquals($user->id, $importLog->user_id);
        $this->assertEquals($user->id, $importLog->user->id);
    }

    public function test_import_log_recipe_relationship_works_with_uuid()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $importLog = ImportLog::factory()->for($user)->for($recipe, 'recipe')->create();

        $this->assertEquals($recipe->id, $importLog->recipe_id);
        $this->assertEquals($recipe->id, $importLog->recipe->id);
    }

    public function test_recipe_routing_works_with_uuid_id()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response = $this->get(route('recipes.show.en', [$recipe->public_id, 'test-recipe']));

        $response->assertStatus(200);
        $response->assertSee('Test Recipe');
    }

    public function test_recipe_edit_route_works_with_uuid_id()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response = $this->actingAs($user)
            ->get(route('recipes.edit.en', $recipe));

        $response->assertStatus(200);
    }

    public function test_user_can_have_multiple_recipes_with_uuid_relationships()
    {
        $user = User::factory()->create();

        $recipe1 = Recipe::factory()->for($user, 'author')->create();
        $recipe2 = Recipe::factory()->for($user, 'author')->create();

        $this->assertNotEquals($recipe1->id, $recipe2->id);
        $this->assertEquals($user->id, $recipe1->user_id);
        $this->assertEquals($user->id, $recipe2->user_id);
        $this->assertEquals($user->id, $recipe1->author->id);
        $this->assertEquals($user->id, $recipe2->author->id);
    }

    public function test_recipe_can_have_multiple_translations_with_uuid_relationships()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $translationEn = $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'English Recipe',
            'slug' => 'english-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $translationNl = $recipe->translations()->create([
            'locale' => 'nl',
            'title' => 'Dutch Recipe',
            'slug' => 'dutch-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $this->assertNotEquals($translationEn->id, $translationNl->id);
        $this->assertEquals($recipe->id, $translationEn->recipe_id);
        $this->assertEquals($recipe->id, $translationNl->recipe_id);
    }

    public function test_soft_delete_works_with_uuid_references()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $recipeId = $recipe->id;
        $translationId = $recipe->translations->first()->id;

        $recipe->delete();

        $this->assertTrue(Recipe::withTrashed()->find($recipeId)->trashed());
        $this->assertNull(Recipe::find($recipeId));

        $translation = RecipeTranslation::withTrashed()->find($translationId);
        $this->assertNotNull($translation);
    }

    public function test_user_soft_delete_cascades_to_recipes_with_uuid()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $user->delete();

        $this->assertTrue($user->trashed());
        $this->assertTrue(Recipe::withTrashed()->find($recipe->id)->trashed());
    }

    public function test_has_uuid_or_id_trait_finds_model_by_uuid()
    {
        $user = User::factory()->create();

        $found = User::findByUuidOrId($user->id);

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_route_model_binding_works_with_uuid()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response = $this->actingAs($user)
            ->get(route('recipes.edit.en', $recipe->id));

        $response->assertStatus(200);
    }

    public function test_recipe_show_with_uuid_public_id()
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'ingredients' => '[]',
            'instructions' => '[]',
        ]);

        $response = $this->get(route('recipes.show.en', [$recipe->public_id, 'test-recipe']));

        $response->assertStatus(200);
        $response->assertSee('Test Recipe');
    }

    private function isValidUuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid) === 1;
    }
}
