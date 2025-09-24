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
        // Add 'local' to the existing enum values
        DB::statement("ALTER TABLE import_logs MODIFY COLUMN source ENUM('structured-data', 'firecrawl', 'open-ai', 'local') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'local' from enum values, but first update any 'local' records to 'structured-data'
        DB::statement("UPDATE import_logs SET source = 'structured-data' WHERE source = 'local'");
        DB::statement("ALTER TABLE import_logs MODIFY COLUMN source ENUM('structured-data', 'firecrawl', 'open-ai') NOT NULL");
    }
};
