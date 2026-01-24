<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill UUIDs for existing records
        DB::table('users')->whereNull('uuid')->update(['uuid' => DB::raw('UUID()')]);
        DB::table('recipes')->whereNull('uuid')->update(['uuid' => DB::raw('UUID()')]);
        DB::table('recipe_translations')->whereNull('uuid')->update(['uuid' => DB::raw('UUID()')]);
        DB::table('import_logs')->whereNull('uuid')->update(['uuid' => DB::raw('UUID()')]);

        // Update foreign key UUID columns
        DB::table('recipe_translations')->update(['recipe_uuid' => DB::raw('(SELECT uuid FROM recipes WHERE recipes.id = recipe_translations.recipe_id)')]);
        DB::table('import_logs')->update(['user_uuid' => DB::raw('(SELECT uuid FROM users WHERE users.id = import_logs.user_id)'), 'recipe_uuid' => DB::raw('(SELECT uuid FROM recipes WHERE recipes.id = import_logs.recipe_id)')]);
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
