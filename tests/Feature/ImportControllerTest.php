<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RecipeParsing\Services\RecipeParsingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_index_page_loads_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('import.index.en'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Import/Index')
            ->has('openAI')
            ->has('firecrawl')
        );
    }

    public function test_import_index_shows_parser_availability(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('import.index.en'));

        $response->assertStatus(200);

        // Check that the service is properly injected and working
        $this->assertInstanceOf(RecipeParsingService::class, app(RecipeParsingService::class));

        // Verify parser availability is correctly reported
        $service = app(RecipeParsingService::class);
        $this->assertTrue($service->isParserAvailable('structured-data'));
    }

    public function test_imported_recipe_defaults_to_private_visibility(): void
    {
        $user = User::factory()->create();

        // Mock the import by directly calling store with valid data
        $response = $this->actingAs($user)->post(route('import.store.en'), [
            'locale' => 'en',
            'title' => 'Imported Recipe',
            'servings' => 4,
            'difficulty' => 'easy',
            'ingredients' => '[]',
            'instructions' => '[]',
            'no_index' => true,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('recipes', [
            'user_id' => $user->id,
            'visibility' => 'private',
        ]);
    }

    public function test_imported_recipe_can_have_public_visibility(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('import.store.en'), [
            'locale' => 'en',
            'title' => 'Imported Recipe Public',
            'servings' => 4,
            'difficulty' => 'easy',
            'ingredients' => '[]',
            'instructions' => '[]',
            'no_index' => true,
            'visibility' => 'public',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('recipes', [
            'user_id' => $user->id,
            'visibility' => 'public',
        ]);
    }
}
