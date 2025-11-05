<?php

namespace Tests\Feature\Jobs;

use App\Jobs\DeleteUserWithRecipes;
use App\Models\ImportLog;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeleteUserWithRecipesJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_is_dispatched_when_user_is_soft_deleted(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $user->delete();

        Queue::assertPushed(DeleteUserWithRecipes::class, function ($job) use ($user) {
            return $job->userId === $user->id;
        });
    }

    public function test_job_is_not_dispatched_when_user_is_force_deleted(): void
    {
        Queue::fake();

        $user = User::factory()->create();

        $user->forceDelete();

        Queue::assertNotPushed(DeleteUserWithRecipes::class);
    }

    public function test_job_soft_deletes_all_user_recipes(): void
    {
        $user = User::factory()->create();
        $recipes = Recipe::factory()->count(5)->for($user, 'author')->create();

        $user->delete();

        $job = new DeleteUserWithRecipes($user->id);
        $job->handle();

        $this->assertEquals(0, Recipe::count());
        $this->assertEquals(5, Recipe::withTrashed()->count());

        foreach ($recipes as $recipe) {
            $this->assertTrue($recipe->fresh()->trashed());
        }
    }

    public function test_job_handles_user_with_no_recipes(): void
    {
        $user = User::factory()->create();

        $user->delete();

        $job = new DeleteUserWithRecipes($user->id);
        $job->handle();

        $this->assertEquals(0, Recipe::count());
    }

    public function test_job_handles_non_existent_user(): void
    {
        $job = new DeleteUserWithRecipes(999999);
        $job->handle();

        $this->assertTrue(true);
    }

    public function test_job_handles_user_that_is_not_soft_deleted(): void
    {
        $user = User::factory()->create();
        Recipe::factory()->count(3)->for($user, 'author')->create();

        $job = new DeleteUserWithRecipes($user->id);
        $job->handle();

        $this->assertEquals(3, Recipe::count());
    }

    public function test_job_handles_large_number_of_recipes(): void
    {
        $user = User::factory()->create();
        Recipe::factory()->count(250)->for($user, 'author')->create();

        $user->delete();

        $job = new DeleteUserWithRecipes($user->id);
        $job->handle();

        $this->assertEquals(0, Recipe::count());
        $this->assertEquals(250, Recipe::withTrashed()->count());
    }

    public function test_job_does_not_delete_recipes_belonging_to_other_users(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Recipe::factory()->count(3)->for($user, 'author')->create();
        Recipe::factory()->count(2)->for($otherUser, 'author')->create();

        $user->delete();

        $job = new DeleteUserWithRecipes($user->id);
        $job->handle();

        $this->assertEquals(2, Recipe::count());
        $this->assertEquals(5, Recipe::withTrashed()->count());
    }

    public function test_job_does_not_delete_already_soft_deleted_recipes(): void
    {
        $user = User::factory()->create();
        $activeRecipe = Recipe::factory()->for($user, 'author')->create();
        $deletedRecipe = Recipe::factory()->for($user, 'author')->trashed()->create();

        $user->delete();

        $job = new DeleteUserWithRecipes($user->id);
        $job->handle();

        $this->assertEquals(0, Recipe::count());
        $this->assertEquals(2, Recipe::withTrashed()->count());
        $this->assertTrue($activeRecipe->fresh()->trashed());
        $this->assertTrue($deletedRecipe->fresh()->trashed());
    }

    public function test_import_logs_remain_intact_after_user_deletion(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::factory()->for($user, 'author')->create();

        $importLog = ImportLog::create([
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
            'url' => 'https://example.com/recipe',
            'source' => 'structured-data',
            'parsed_data' => ['test' => 'data'],
            'credits_used' => 1,
        ]);

        $user->delete();

        $job = new DeleteUserWithRecipes($user->id);
        $job->handle();

        $this->assertDatabaseHas('import_logs', [
            'id' => $importLog->id,
            'user_id' => $user->id,
            'recipe_id' => $recipe->id,
        ]);
    }

    public function test_job_has_unique_id_based_on_user_id(): void
    {
        $job1 = new DeleteUserWithRecipes(1);
        $job2 = new DeleteUserWithRecipes(1);
        $job3 = new DeleteUserWithRecipes(2);

        $this->assertEquals($job1->uniqueId(), $job2->uniqueId());
        $this->assertNotEquals($job1->uniqueId(), $job3->uniqueId());
    }
}
