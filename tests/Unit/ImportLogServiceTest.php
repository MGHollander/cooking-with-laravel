<?php

namespace Tests\Unit;

use App\Models\ImportLog;
use App\Models\Recipe;
use App\Models\User;
use App\Services\ImportLogService;
use App\Services\RecipeParsing\Data\ParsedRecipeData;
use App\Support\FileHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportLogServiceTest extends TestCase
{
    use RefreshDatabase;

    private ImportLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ImportLogService;
    }

    public function test_log_successful_import_creates_import_log(): void
    {
        $user = User::factory()->create();
        $url = 'https://example.com/recipe?utm_source=test#section';
        $source = 'structured-data';

        $parsedData = new ParsedRecipeData(
            title: 'Test Recipe',
            ingredients: ['1 cup flour', '2 eggs'],
            steps: ['Mix flour', 'Add eggs'],
            description: 'A test recipe',
            keywords: 'test, recipe',
            prepTime: 'PT15M',
            cookTime: 'PT30M',
            totalTime: 'PT45M',
            yield: 4,
            difficulty: 'easy',
            images: ['https://example.com/image.jpg'],
            url: $url
        );

        $importLog = $this->service->logSuccessfulImport($url, $source, $user, $parsedData);

        $this->assertInstanceOf(ImportLog::class, $importLog);
        $this->assertEquals(FileHelper::cleanUrl($url), $importLog->url);
        $this->assertEquals($source, $importLog->source);
        $this->assertEquals($user->id, $importLog->user_id);
        $this->assertNull($importLog->recipe_id); // Recipe is not created yet
        $this->assertEquals($parsedData->toArray(), $importLog->parsed_data);

        $this->assertDatabaseHas('import_logs', [
            'url' => 'https://example.com/recipe',
            'source' => 'structured-data',
            'user_id' => $user->id,
            'recipe_id' => null,
        ]);
    }

    public function test_log_successful_import_with_recipe_creates_import_log_with_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        $url = 'https://example.com/recipe?utm_source=test#section';
        $source = 'structured-data';

        $parsedData = new ParsedRecipeData(
            title: 'Test Recipe',
            ingredients: ['1 cup flour', '2 eggs'],
            steps: ['Mix flour', 'Add eggs'],
            description: 'A test recipe',
            keywords: 'test, recipe',
            prepTime: 'PT15M',
            cookTime: 'PT30M',
            totalTime: 'PT45M',
            yield: 4,
            difficulty: 'easy',
            images: ['https://example.com/image.jpg'],
            url: $url
        );

        $importLog = $this->service->logSuccessfulImport($url, $source, $user, $parsedData, $recipe);

        $this->assertInstanceOf(ImportLog::class, $importLog);
        $this->assertEquals(FileHelper::cleanUrl($url), $importLog->url);
        $this->assertEquals($source, $importLog->source);
        $this->assertEquals($user->id, $importLog->user_id);
        $this->assertEquals($recipe->id, $importLog->recipe_id);
        $this->assertEquals($parsedData->toArray(), $importLog->parsed_data);

        $this->assertDatabaseHas('import_logs', [
            'url' => 'https://example.com/recipe',
            'source' => 'structured-data',
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_update_import_log_with_recipe(): void
    {
        $importLog = ImportLog::factory()->create(['recipe_id' => null]);
        $recipe = Recipe::factory()->create();

        $updatedLog = $this->service->updateImportLogWithRecipe($importLog, $recipe);

        $this->assertEquals($recipe->id, $updatedLog->recipe_id);
        $this->assertDatabaseHas('import_logs', [
            'id' => $importLog->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_get_import_logs_for_user_returns_user_logs(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create logs for user1
        ImportLog::factory()->count(3)->create(['user_id' => $user1->id]);
        // Create logs for user2
        ImportLog::factory()->count(2)->create(['user_id' => $user2->id]);

        $user1Logs = $this->service->getImportLogsForUser($user1);
        $user2Logs = $this->service->getImportLogsForUser($user2);

        $this->assertCount(3, $user1Logs);
        $this->assertCount(2, $user2Logs);

        // Verify all logs belong to the correct user
        $user1Logs->each(fn ($log) => $this->assertEquals($user1->id, $log->user_id));
        $user2Logs->each(fn ($log) => $this->assertEquals($user2->id, $log->user_id));
    }

    public function test_get_import_logs_for_user_respects_limit(): void
    {
        $user = User::factory()->create();
        ImportLog::factory()->count(10)->create(['user_id' => $user->id]);

        $logs = $this->service->getImportLogsForUser($user, 5);

        $this->assertCount(5, $logs);
    }

    public function test_get_import_stats_for_user_returns_correct_stats(): void
    {
        $user = User::factory()->create();

        // Create logs with different sources
        ImportLog::factory()->count(3)->create([
            'user_id' => $user->id,
            'source' => 'structured-data',
        ]);
        ImportLog::factory()->count(2)->create([
            'user_id' => $user->id,
            'source' => 'firecrawl',
        ]);
        ImportLog::factory()->count(1)->create([
            'user_id' => $user->id,
            'source' => 'open-ai',
        ]);

        $stats = $this->service->getImportStatsForUser($user);

        $this->assertEquals(6, $stats['total_imports']);
        $this->assertEquals(3, $stats['imports_by_source']['structured-data']);
        $this->assertEquals(2, $stats['imports_by_source']['firecrawl']);
        $this->assertEquals(1, $stats['imports_by_source']['open-ai']);
        $this->assertNotNull($stats['most_recent_import']);
    }

    public function test_has_user_imported_url_returns_true_when_url_exists(): void
    {
        $user = User::factory()->create();
        $url = 'https://example.com/recipe?utm_source=test#section';
        $cleanUrl = 'https://example.com/recipe';

        ImportLog::factory()->create([
            'user_id' => $user->id,
            'url' => $cleanUrl,
        ]);

        $this->assertTrue($this->service->hasUserImportedUrl($user, $url));
        $this->assertTrue($this->service->hasUserImportedUrl($user, $cleanUrl));
    }

    public function test_has_user_imported_url_returns_false_when_url_does_not_exist(): void
    {
        $user = User::factory()->create();
        $url = 'https://example.com/recipe';

        $this->assertFalse($this->service->hasUserImportedUrl($user, $url));
    }

    public function test_get_last_import_for_url_returns_most_recent_import(): void
    {
        $url = 'https://example.com/recipe?param=value#hash';
        $cleanUrl = 'https://example.com/recipe';

        $olderLog = ImportLog::factory()->create([
            'url' => $cleanUrl,
            'created_at' => now()->subDays(2),
        ]);

        $newerLog = ImportLog::factory()->create([
            'url' => $cleanUrl,
            'created_at' => now()->subDay(),
        ]);

        $lastImport = $this->service->getLastImportForUrl($url);

        $this->assertNotNull($lastImport);
        $this->assertEquals($newerLog->id, $lastImport->id);
    }

    public function test_get_last_import_for_url_returns_null_when_no_imports(): void
    {
        $url = 'https://example.com/nonexistent-recipe';

        $lastImport = $this->service->getLastImportForUrl($url);

        $this->assertNull($lastImport);
    }

    public function test_get_last_import_for_url_excludes_local_source(): void
    {
        $url = 'https://example.com/recipe';
        $cleanUrl = 'https://example.com/recipe';

        // Create a local source import (should be excluded)
        $localLog = ImportLog::factory()->create([
            'url' => $cleanUrl,
            'source' => 'local',
            'created_at' => now()->subMinutes(10),
        ]);

        // Create a non-local import (should be returned)
        $nonLocalLog = ImportLog::factory()->create([
            'url' => $cleanUrl,
            'source' => 'structured-data',
            'created_at' => now()->subMinutes(20),
        ]);

        $lastImport = $this->service->getLastImportForUrl($url);

        $this->assertNotNull($lastImport);
        $this->assertEquals($nonLocalLog->id, $lastImport->id);
        $this->assertEquals('structured-data', $lastImport->source);
    }

    public function test_get_last_import_for_url_returns_null_when_only_local_imports(): void
    {
        $url = 'https://example.com/recipe';
        $cleanUrl = 'https://example.com/recipe';

        // Create only local source imports
        ImportLog::factory()->count(3)->create([
            'url' => $cleanUrl,
            'source' => 'local',
        ]);

        $lastImport = $this->service->getLastImportForUrl($url);

        $this->assertNull($lastImport);
    }

    public function test_get_last_non_local_import_for_url_returns_most_recent_non_local(): void
    {
        $url = 'https://example.com/recipe?param=value#hash';
        $cleanUrl = 'https://example.com/recipe';

        // Create local import (should be ignored)
        ImportLog::factory()->create([
            'url' => $cleanUrl,
            'source' => 'local',
            'created_at' => now()->subMinutes(5),
        ]);

        // Create older non-local import
        $olderNonLocal = ImportLog::factory()->create([
            'url' => $cleanUrl,
            'source' => 'structured-data',
            'created_at' => now()->subMinutes(20),
        ]);

        // Create newer non-local import (should be returned)
        $newerNonLocal = ImportLog::factory()->create([
            'url' => $cleanUrl,
            'source' => 'firecrawl',
            'created_at' => now()->subMinutes(10),
        ]);

        $lastNonLocalImport = $this->service->getLastNonLocalImportForUrl($url);

        $this->assertNotNull($lastNonLocalImport);
        $this->assertEquals($newerNonLocal->id, $lastNonLocalImport->id);
        $this->assertEquals('firecrawl', $lastNonLocalImport->source);
    }

    public function test_get_last_non_local_import_for_url_returns_null_when_only_local(): void
    {
        $url = 'https://example.com/recipe';
        $cleanUrl = 'https://example.com/recipe';

        // Create only local imports
        ImportLog::factory()->count(2)->create([
            'url' => $cleanUrl,
            'source' => 'local',
        ]);

        $lastNonLocalImport = $this->service->getLastNonLocalImportForUrl($url);

        $this->assertNull($lastNonLocalImport);
    }

    public function test_create_local_import_log_creates_local_source_log(): void
    {
        $user = User::factory()->create();
        $url = 'https://example.com/recipe?utm_source=test#section';
        $parsedData = [
            'title' => 'Test Recipe',
            'ingredients' => ['1 cup flour', '2 eggs'],
            'steps' => ['Mix flour', 'Add eggs'],
            'description' => 'A test recipe',
        ];

        $importLog = $this->service->createLocalImportLog($url, $user, $parsedData);

        $this->assertInstanceOf(ImportLog::class, $importLog);
        $this->assertEquals(FileHelper::cleanUrl($url), $importLog->url);
        $this->assertEquals('local', $importLog->source);
        $this->assertEquals($user->id, $importLog->user_id);
        $this->assertNull($importLog->recipe_id);
        $this->assertEquals($parsedData, $importLog->parsed_data);

        $this->assertDatabaseHas('import_logs', [
            'url' => 'https://example.com/recipe',
            'source' => 'local',
            'user_id' => $user->id,
            'recipe_id' => null,
        ]);
    }

    public function test_create_local_import_log_with_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->create();
        $url = 'https://example.com/recipe';
        $parsedData = [
            'title' => 'Test Recipe',
            'ingredients' => ['1 cup flour'],
            'steps' => ['Mix'],
        ];

        $importLog = $this->service->createLocalImportLog($url, $user, $parsedData, $recipe);

        $this->assertEquals($recipe->id, $importLog->recipe_id);
        $this->assertDatabaseHas('import_logs', [
            'url' => $url,
            'source' => 'local',
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_get_user_import_for_url_returns_users_latest_import(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $url = 'https://example.com/recipe?param=value';
        $cleanUrl = 'https://example.com/recipe';

        // Create import for user2 (should be ignored)
        ImportLog::factory()->create([
            'user_id' => $user2->id,
            'url' => $cleanUrl,
            'created_at' => now()->subMinutes(5),
        ]);

        // Create older import for user1
        $olderImport = ImportLog::factory()->create([
            'user_id' => $user1->id,
            'url' => $cleanUrl,
            'created_at' => now()->subMinutes(20),
        ]);

        // Create newer import for user1 (should be returned)
        $newerImport = ImportLog::factory()->create([
            'user_id' => $user1->id,
            'url' => $cleanUrl,
            'created_at' => now()->subMinutes(10),
        ]);

        $userImport = $this->service->getUserImportForUrl($user1, $url);

        $this->assertNotNull($userImport);
        $this->assertEquals($newerImport->id, $userImport->id);
        $this->assertEquals($user1->id, $userImport->user_id);
    }

    public function test_get_user_import_for_url_returns_null_when_user_has_no_imports(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $url = 'https://example.com/recipe';
        $cleanUrl = 'https://example.com/recipe';

        // Create import for different user
        ImportLog::factory()->create([
            'user_id' => $user2->id,
            'url' => $cleanUrl,
        ]);

        $userImport = $this->service->getUserImportForUrl($user1, $url);

        $this->assertNull($userImport);
    }
}
