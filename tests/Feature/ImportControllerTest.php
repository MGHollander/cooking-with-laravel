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

        $response = $this->actingAs($user)->get(route('import.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn($page) =>
            $page->component('Import/Index')
                ->has('openAI')
                ->has('firecrawl')
        );
    }

    public function test_import_index_shows_parser_availability(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('import.index'));

        $response->assertStatus(200);

        // Check that the service is properly injected and working
        $this->assertInstanceOf(RecipeParsingService::class, app(RecipeParsingService::class));

        // Verify parser availability is correctly reported
        $service = app(RecipeParsingService::class);
        $this->assertTrue($service->isParserAvailable('structured-data'));
    }

    public function test_import_create_accepts_post_request(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('import.create'), [
            'url' => 'https://example.com/recipe',
            'parser' => 'structured-data',
        ]);

        // The response should not be a 405 Method Not Allowed
        $this->assertNotEquals(405, $response->getStatusCode());
    }

    public function test_import_create_rejects_get_request(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('import.create') . '?url=https://example.com/recipe&parser=structured-data');

        // Should return 405 Method Not Allowed since we changed from GET to POST
        $response->assertStatus(405);
    }
}
