<?php

namespace App\Services;

use App\Models\ImportLog;
use App\Models\Recipe;
use App\Models\User;
use App\Services\RecipeParsing\Data\ParsedRecipeData;
use App\Support\FileHelper;

class ImportLogService
{
    /**
     * Log a successful recipe import.
     */
    public function logSuccessfulImport(
        string $url,
        string $source,
        User $user,
        ParsedRecipeData $parsedData,
        ?Recipe $recipe = null
    ): ImportLog {
        return ImportLog::create([
            'url' => FileHelper::cleanUrl($url),
            'source' => $source,
            'user_id' => $user->id,
            'recipe_id' => $recipe?->id,
            'parsed_data' => $parsedData->toArray(),
        ]);
    }

    /**
     * Update an import log with the created recipe.
     */
    public function updateImportLogWithRecipe(ImportLog $importLog, Recipe $recipe): ImportLog
    {
        $importLog->update(['recipe_id' => $recipe->id]);
        return $importLog->fresh();
    }

    /**
     * Get import logs for a specific user.
     */
    public function getImportLogsForUser(User $user, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ImportLog::where('user_id', $user->id)
            ->with(['recipe'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get import statistics for a specific user.
     */
    public function getImportStatsForUser(User $user): array
    {
        $logs = ImportLog::where('user_id', $user->id)->get();

        return [
            'total_imports' => $logs->count(),
            'imports_by_source' => $logs->groupBy('source')->map->count(),
            'most_recent_import' => $logs->sortByDesc('created_at')->first()?->created_at,
        ];
    }

    /**
     * Check if a URL has already been imported by a user.
     */
    public function hasUserImportedUrl(User $user, string $url): bool
    {
        $cleanUrl = FileHelper::cleanUrl($url);
        
        return ImportLog::where('user_id', $user->id)
            ->where('url', $cleanUrl)
            ->exists();
    }

    /**
     * Get the most recently imported recipe for a URL by any user.
     */
    public function getLastImportForUrl(string $url): ?ImportLog
    {
        $cleanUrl = FileHelper::cleanUrl($url);
        
        return ImportLog::where('url', $cleanUrl)
            ->with(['recipe', 'user'])
            ->latest()
            ->first();
    }
}