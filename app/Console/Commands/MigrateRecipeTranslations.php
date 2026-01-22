<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateRecipeTranslations extends Command
{
    protected $signature = 'recipes:migrate-translations 
                          {--locale=en : Default locale for existing recipes}
                          {--chunk=50 : Number of recipes to process at once}
                          {--dry-run : Run without making changes}';

    protected $description = 'Migrate existing recipe data to translation table';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $chunk = (int) $this->option('chunk');
        $locale = $this->option('locale');

        if (! in_array($locale, ['en', 'nl'])) {
            $this->error('Invalid locale. Must be "en" or "nl".');

            return 1;
        }

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $this->info("Starting recipe translation migration (locale: {$locale})...");

        $count = 0;

        DB::transaction(function () use ($dryRun, $chunk, $locale, &$count) {
            $totalRecipes = DB::table('recipes')->count();
            $processed = 0;

            DB::table('recipes')
                ->orderBy('id')
                ->chunk($chunk, function ($recipes) use ($dryRun, $locale, &$count, &$processed, $totalRecipes) {
                    foreach ($recipes as $recipeData) {
                        $translationData = [
                            'recipe_id' => $recipeData->id,
                            'locale' => $locale,
                            'slug' => $recipeData->slug,
                            'title' => $recipeData->title,
                            'summary' => $recipeData->summary,
                            'ingredients' => $recipeData->ingredients,
                            'instructions' => $recipeData->instructions,
                            'created_at' => $recipeData->created_at ?? now(),
                            'updated_at' => $recipeData->updated_at ?? now(),
                        ];

                        $processed++;
                        $this->info("[{$processed}/{$totalRecipes}] Processing recipe #{$recipeData->id}: {$recipeData->title}");

                        if (! $dryRun) {
                            DB::table('recipe_translations')->updateOrInsert(
                                [
                                    'recipe_id' => $recipeData->id,
                                    'locale' => $locale,
                                ],
                                $translationData
                            );
                            $count++;
                        }

                        $this->line("  ✓ Created {$locale} translation");
                    }
                });
        });

        $this->info("Migration complete! Processed {$count} recipes.");

        if (! $dryRun) {
            $this->warn('');
            $this->warn('NEXT STEPS:');
            $this->warn('1. Verify translations in database');
            $this->warn('2. Test recipe viewing and editing');
            $this->warn('3. Run migration to drop old columns from recipes table');
            $this->warn('   php artisan migrate');
        }

        return 0;
    }
}
