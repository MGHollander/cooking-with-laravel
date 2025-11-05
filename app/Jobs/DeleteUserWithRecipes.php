<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteUserWithRecipes implements ShouldQueue, ShouldBeUnique
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public int $userId)
    {
    }

    public function uniqueId(): int
    {
        return $this->userId;
    }

    public function handle(): void
    {
        $user = User::withTrashed()->find($this->userId);

        if (!$user) {
            Log::warning("DeleteUserWithRecipes: User {$this->userId} not found");
            return;
        }

        if (!$user->trashed()) {
            Log::warning("DeleteUserWithRecipes: User {$this->userId} is not soft deleted");
            return;
        }

        $totalRecipes = $user->recipes()->withTrashed()->count();

        if ($totalRecipes === 0) {
            Log::info("DeleteUserWithRecipes: User {$this->userId} has no recipes to delete");
            return;
        }

        Log::info("DeleteUserWithRecipes: Starting deletion of {$totalRecipes} recipes for user {$this->userId}");

        $deletedCount = 0;

        $user->recipes()->chunk(100, function ($recipes) use (&$deletedCount) {
            foreach ($recipes as $recipe) {
                if (!$recipe->trashed()) {
                    $recipe->delete();
                    $deletedCount++;
                }
            }
        });

        Log::info("DeleteUserWithRecipes: Completed deletion of {$deletedCount} recipes for user {$this->userId}");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("DeleteUserWithRecipes: Failed to delete recipes for user {$this->userId}", [
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
