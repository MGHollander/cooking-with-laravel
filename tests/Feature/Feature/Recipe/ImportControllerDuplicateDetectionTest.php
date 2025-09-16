<?php

namespace Tests\Feature\Feature\Recipe;

use App\Models\ImportLog;
use App\Models\Recipe;
use App\Models\User;
use App\Services\RecipeParsing\Data\ParsedRecipeData;
use App\Services\RecipeParsing\Services\RecipeParsingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportControllerDuplicateDetectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $testUrl = 'https://example.com/recipe';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_create_returns_flash_message_when_current_user_already_imported_recipe(): void
    {
        // Create a recipe and import log for the current user
        $recipe = Recipe::factory()->create(['user_id' => $this->user->id]);
        ImportLog::factory()->create([
            'user_id' => $this->user->id,
            'url' => $this->testUrl,
            'recipe_id' => $recipe->id,
            'source' => 'structured-data',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('import.create'), [
                'url' => $this->testUrl,
                'parser' => 'structured-data',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('info');

        $flashMessage = session('info');
        $this->assertStringContainsString('Je hebt dit recept al geïmporteerd', $flashMessage);
        $this->assertStringContainsString($recipe->title, $flashMessage);
        $this->assertStringContainsString(route('recipes.show', $recipe->slug), $flashMessage);
    }

    public function test_create_shows_form_with_existing_data_when_other_user_imported_recipe(): void
    {
        $otherUser = User::factory()->create();
        $parsedData = [
            'title' => 'Existing Recipe',
            'ingredients' => ['1 cup flour', '2 eggs'],
            'steps' => ['Mix ingredients', 'Bake for 30 minutes'],
            'description' => 'A delicious recipe',
            'keywords' => 'recipe, test',
            'prepTime' => 'PT15M',
            'cookTime' => 'PT30M',
            'totalTime' => 'PT45M',
            'yield' => 4,
            'difficulty' => 'easy',
            'images' => ['https://example.com/image.jpg'],
            'url' => $this->testUrl,
        ];

        // Create import log for another user
        ImportLog::factory()->create([
            'user_id' => $otherUser->id,
            'url' => $this->testUrl,
            'source' => 'structured-data',
            'parsed_data' => $parsedData,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('import.create'), [
                'url' => $this->testUrl,
                'parser' => 'structured-data',
            ]);

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Import/Form')
                ->has('recipe')
                ->has('import_log_id')
                ->where('url', $this->testUrl)
                ->where('recipe.title', 'Existing Recipe')
                ->where('recipe.ingredients', "1 cup flour\n2 eggs") // ImportResource joins arrays with \n
            );

        // Verify a local import log was created for current user
        $this->assertDatabaseHas('import_logs', [
            'user_id' => $this->user->id,
            'url' => $this->testUrl,
            'source' => 'local',
        ]);
    }

    public function test_create_ignores_local_source_when_finding_existing_imports(): void
    {
        $otherUser = User::factory()->create();
        $parsedData = [
            'title' => 'Original Recipe',
            'ingredients' => ['1 cup flour'],
            'steps' => ['Mix and bake'],
            'description' => 'Original description',
        ];

        // Create original import with actual API source
        $originalImport = ImportLog::factory()->create([
            'user_id' => $otherUser->id,
            'url' => $this->testUrl,
            'source' => 'firecrawl',
            'parsed_data' => $parsedData,
            'created_at' => now()->subHours(2),
        ]);

        // Create newer local import (should be ignored)
        ImportLog::factory()->create([
            'user_id' => User::factory()->create()->id,
            'url' => $this->testUrl,
            'source' => 'local',
            'parsed_data' => [
                'title' => 'Local Copy Recipe',
                'ingredients' => ['Different ingredients'],
                'steps' => ['Different steps'],
            ],
            'created_at' => now()->subHours(1),
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('import.create'), [
                'url' => $this->testUrl,
                'parser' => 'structured-data',
            ]);

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Import/Form')
                ->where('recipe.title', 'Original Recipe') // Should use original data, not local
                ->where('recipe.ingredients', '1 cup flour') // Single ingredient doesn't get \n
            );
    }

    public function test_create_falls_back_to_api_parsing_when_existing_data_invalid(): void
    {
        $otherUser = User::factory()->create();

        // Create import log with invalid parsed data
        ImportLog::factory()->create([
            'user_id' => $otherUser->id,
            'url' => $this->testUrl,
            'source' => 'structured-data',
            'parsed_data' => [
                // Missing required fields like title, ingredients, steps
                'description' => 'Only description',
            ],
        ]);

        // Mock the recipe parsing service to return valid data
        $this->mock(RecipeParsingService::class, function ($mock) {
            $parsedData = new ParsedRecipeData(
                title: 'Fresh API Recipe',
                ingredients: ['1 cup sugar'],
                steps: ['Mix and serve'],
                description: 'From API'
            );
            $mock->shouldReceive('parseWithParser')
                ->once()
                ->with($this->testUrl, 'structured-data')
                ->andReturn($parsedData);
        });

        $response = $this->actingAs($this->user)
            ->post(route('import.create'), [
                'url' => $this->testUrl,
                'parser' => 'structured-data',
            ]);

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Import/Form')
                ->where('recipe.title', 'Fresh API Recipe')
            );

        // Verify new import log was created with API source, not local
        $this->assertDatabaseHas('import_logs', [
            'user_id' => $this->user->id,
            'url' => $this->testUrl,
            'source' => 'structured-data', // API source, not local
        ]);
    }

    public function test_create_proceeds_with_api_parsing_when_no_existing_imports(): void
    {
        // Mock the recipe parsing service
        $this->mock(RecipeParsingService::class, function ($mock) {
            $parsedData = new ParsedRecipeData(
                title: 'New Recipe',
                ingredients: ['1 cup water'],
                steps: ['Boil water'],
                description: 'Simple recipe'
            );
            $mock->shouldReceive('parseWithParser')
                ->once()
                ->with($this->testUrl, 'structured-data')
                ->andReturn($parsedData);
        });

        $response = $this->actingAs($this->user)
            ->post(route('import.create'), [
                'url' => $this->testUrl,
                'parser' => 'structured-data',
            ]);

        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Import/Form')
                ->where('recipe.title', 'New Recipe')
            );

        // Verify new import log was created with API source
        $this->assertDatabaseHas('import_logs', [
            'user_id' => $this->user->id,
            'url' => $this->testUrl,
            'source' => 'structured-data',
        ]);
    }

    public function test_create_does_not_block_when_user_has_no_recipe_for_import_log(): void
    {
        // Create import log for current user but without associated recipe
        $userImport = ImportLog::factory()->create([
            'user_id' => $this->user->id,
            'url' => $this->testUrl,
            'recipe_id' => null, // No recipe associated
            'source' => 'structured-data',
            'parsed_data' => [
                'title' => 'Previous Attempt',
                'ingredients' => ['1 cup milk'],
                'steps' => ['Heat milk'],
                'description' => 'User\'s previous attempt without recipe',
            ],
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('import.create'), [
                'url' => $this->testUrl,
                'parser' => 'structured-data',
            ]);

        // Should not be blocked since there's no associated recipe
        // But should reuse the user's own previous import data
        $response->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->component('Import/Form')
                ->where('recipe.title', 'Previous Attempt')
            );

        // Verify a new local import log was created for current user
        $this->assertDatabaseHas('import_logs', [
            'user_id' => $this->user->id,
            'url' => $this->testUrl,
            'source' => 'local',
        ]);
    }

    public function test_create_uses_most_recent_user_import_when_multiple_exist(): void
    {
        $olderRecipe = Recipe::factory()->create(['user_id' => $this->user->id, 'title' => 'Older Recipe']);
        $newerRecipe = Recipe::factory()->create(['user_id' => $this->user->id, 'title' => 'Newer Recipe']);

        // Create older import
        ImportLog::factory()->create([
            'user_id' => $this->user->id,
            'url' => $this->testUrl,
            'recipe_id' => $olderRecipe->id,
            'source' => 'structured-data',
            'created_at' => now()->subHours(2),
        ]);

        // Create newer import (should be used for blocking message)
        ImportLog::factory()->create([
            'user_id' => $this->user->id,
            'url' => $this->testUrl,
            'recipe_id' => $newerRecipe->id,
            'source' => 'firecrawl',
            'created_at' => now()->subHours(1),
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('import.create'), [
                'url' => $this->testUrl,
                'parser' => 'structured-data',
            ]);

        $response->assertRedirect()
            ->assertSessionHas('info');

        $flashMessage = session('info');
        $this->assertStringContainsString('Newer Recipe', $flashMessage); // Should reference newer recipe
    }
}
