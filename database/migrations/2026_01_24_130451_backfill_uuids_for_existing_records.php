<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill UUIDs for existing records
        $this->backfillUuidV7('users');
        $this->backfillUuidV7('recipes');
        $this->backfillUuidV7('recipe_translations');
        $this->backfillUuidV7('import_logs');

        // Update foreign key UUID columns
        DB::table('recipe_translations')->update(['recipe_uuid' => DB::raw('(SELECT uuid FROM recipes WHERE recipes.id = recipe_translations.recipe_id)')]);
        DB::table('import_logs')->update(['user_uuid' => DB::raw('(SELECT uuid FROM users WHERE users.id = import_logs.user_id)'), 'recipe_uuid' => DB::raw('(SELECT uuid FROM recipes WHERE recipes.id = import_logs.recipe_id)')]);
    }

    protected function backfillUuidV7(string $table): void
    {
        DB::table($table)->whereNull('uuid')->orderBy('id')->chunk(100, function ($records) use ($table) {
            foreach ($records as $record) {
                DB::table($table)->where('id', $record->id)->update([
                    'uuid' => (string) Str::uuid7(),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nullify UUID columns (reverse backfill)
        DB::table('users')->update(['uuid' => null]);
        DB::table('recipes')->update(['uuid' => null]);
        DB::table('recipe_translations')->update(['uuid' => null, 'recipe_uuid' => null]);
        DB::table('import_logs')->update(['uuid' => null, 'user_uuid' => null, 'recipe_uuid' => null]);
    }
};
