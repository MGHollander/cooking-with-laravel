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
     * Get the most recently imported recipe for a URL by any user, excluding 'local' source.
     */
    public function getLastImportForUrl(string $url): ?ImportLog
    {
        $cleanUrl = FileHelper::cleanUrl($url);

        return ImportLog::where('url', $cleanUrl)
            ->where('source', '!=', 'local')
            ->with(['recipe', 'user'])
            ->latest()
            ->first();
    }

    /**
     * Get the most recently imported recipe for a URL by any user, excluding 'local' source.
     */
    public function getLastNonLocalImportForUrl(string $url): ?ImportLog
    {
        $cleanUrl = FileHelper::cleanUrl($url);

        return ImportLog::where('url', $cleanUrl)
            ->where('source', '!=', 'local')
            ->with(['recipe', 'user'])
            ->latest()
            ->first();
    }

    /**
     * Create a new import log with 'local' source using existing parsed data.
     */
    public function createLocalImportLog(
        string $url,
        User $user,
        array $parsedData,
        ?Recipe $recipe = null
    ): ImportLog {
        return ImportLog::create([
            'url' => FileHelper::cleanUrl($url),
            'source' => 'local',
            'user_id' => $user->id,
            'recipe_id' => $recipe?->id,
            'parsed_data' => $parsedData,
        ]);
    }

    /**
     * Get the existing import log for a user and URL, including recipe if available.
     */
    public function getUserImportForUrl(User $user, string $url): ?ImportLog
    {
        $cleanUrl = FileHelper::cleanUrl($url);

        return ImportLog::where('user_id', $user->id)
            ->where('url', $cleanUrl)
            ->with(['recipe'])
            ->latest()
            ->first();
    }
}
