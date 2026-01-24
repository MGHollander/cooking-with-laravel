<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'title', 'summary', 'ingredients', 'instructions']);
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('instructions')->nullable();
        });
    }
};
