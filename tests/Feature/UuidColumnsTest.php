<?php

namespace Tests\Feature;

use App\Models\ImportLog;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UuidColumnsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_set_uuids_when_creating_domain_models(): void
    {
        // Given
        $user = User::factory()->create();

        // When
        $recipe = Recipe::factory()->create(['user_id' => $user->id]);
        $translation = $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test recipe',
            'summary' => null,
            'ingredients' => 'Ingredients',
            'instructions' => 'Instructions',
        ]);
        $importLog = ImportLog::factory()->create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'credits_used' => 0,
        ]);

        // Then
        $this->assertNotNull($recipe->uuid);
        $this->assertNotNull($translation->uuid);
        $this->assertSame($recipe->uuid, $translation->recipe_uuid);
        $this->assertNotNull($importLog->uuid);
        $this->assertSame($recipe->uuid, $importLog->recipe_uuid);
        $this->assertSame($user->uuid, $importLog->user_uuid);
    }
}
