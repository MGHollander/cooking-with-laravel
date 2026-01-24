<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        Schema::table('recipe_translations', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->uuid('recipe_uuid')->nullable()->index()->after('recipe_id');
        });

        Schema::table('import_logs', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->uuid('user_uuid')->nullable()->index()->after('user_id');
            $table->uuid('recipe_uuid')->nullable()->index()->after('recipe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('import_logs', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'user_uuid', 'recipe_uuid']);
        });

        Schema::table('recipe_translations', function (Blueprint $table) {
            $table->dropColumn(['uuid', 'recipe_uuid']);
        });

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
