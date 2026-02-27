<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop foreign key constraints and indices that use the numeric IDs
        Schema::table('recipe_translations', function (Blueprint $table) {
            $table->dropForeign(['recipe_id']);
            $table->dropUnique(['recipe_id', 'locale']);
        });

        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['recipe_id']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        // 2. Add UUID foreign key columns
        Schema::table('recipes', function (Blueprint $table) {
            $table->uuid('user_uuid')->nullable()->after('user_id');
        });

        Schema::table('recipe_translations', function (Blueprint $table) {
            $table->uuid('recipe_uuid')->nullable()->after('recipe_id');
        });

        Schema::table('import_logs', function (Blueprint $table) {
            $table->uuid('user_uuid')->nullable()->after('user_id');
            $table->uuid('recipe_uuid')->nullable()->after('recipe_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->string('user_uuid', 36)->nullable()->after('user_id');
        });

        // 3. Populate UUID foreign key columns
        DB::statement('UPDATE recipes r JOIN users u ON r.user_id = u.id SET r.user_uuid = u.uuid');
        DB::statement('UPDATE recipe_translations rt JOIN recipes r ON rt.recipe_id = r.id SET rt.recipe_uuid = r.uuid');
        DB::statement('UPDATE import_logs il JOIN users u ON il.user_id = u.id SET il.user_uuid = u.uuid');
        DB::statement('UPDATE import_logs il JOIN recipes r ON il.recipe_id = r.id SET il.recipe_uuid = r.uuid');
        DB::statement('UPDATE sessions s JOIN users u ON s.user_id = u.id SET s.user_uuid = u.uuid');

        // 4. Update morphs - taggables, media, personal_access_tokens
        Schema::table('taggables', function (Blueprint $table) {
            $table->string('taggable_id', 36)->change();
        });

        Schema::table('media', function (Blueprint $table) {
            $table->string('model_id', 36)->change();
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->string('tokenable_id', 36)->change();
        });

        // Update morph data to UUIDs
        DB::statement("UPDATE taggables t JOIN recipes r ON t.taggable_id = r.id SET t.taggable_id = r.uuid WHERE t.taggable_type = 'App\\\\Models\\\\Recipe'");
        DB::statement("UPDATE media m JOIN recipes r ON m.model_id = r.id SET m.model_id = r.uuid WHERE m.model_type = 'App\\\\Models\\\\Recipe'");
        DB::statement("UPDATE personal_access_tokens p JOIN users u ON p.tokenable_id = u.id SET p.tokenable_id = u.uuid WHERE p.tokenable_type = 'App\\\\Models\\\\User'");

        // 5. Change primary keys for referenced tables
        $tablesToMigrate = ['users', 'recipes', 'recipe_translations', 'import_logs'];

        foreach ($tablesToMigrate as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Remove auto-increment first
                $table->bigInteger('id')->autoIncrement(false)->change();
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropPrimary();
                $table->dropColumn('id');
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('uuid', 'id');
            });

            Schema::table($tableName, function (Blueprint $table) {
                $table->primary('id');
            });
        }

        // 6. Cleanup foreign key columns in referencing tables
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->renameColumn('user_uuid', 'user_id');
        });

        Schema::table('recipe_translations', function (Blueprint $table) {
            $table->dropColumn('recipe_id');
            $table->renameColumn('recipe_uuid', 'recipe_id');
        });

        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('recipe_id');
            $table->renameColumn('user_uuid', 'user_id');
            $table->renameColumn('recipe_uuid', 'recipe_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->renameColumn('user_uuid', 'user_id');
        });

        // 7. Re-add foreign key constraints and indices
        Schema::table('recipes', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('recipe_translations', function (Blueprint $table) {
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('cascade');
            $table->unique(['recipe_id', 'locale']);
        });

        Schema::table('import_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('recipe_id')->references('id')->on('recipes')->onDelete('set null');
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
