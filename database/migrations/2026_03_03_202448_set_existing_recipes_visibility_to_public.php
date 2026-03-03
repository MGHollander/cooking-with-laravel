<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('recipes')->update(['visibility' => 'public']);
    }

    public function down(): void
    {
        // Cannot reliably restore previous values, so no-op
    }
};
