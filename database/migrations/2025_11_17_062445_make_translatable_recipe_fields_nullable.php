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
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
            $table->string('title')->nullable()->change();
            $table->text('summary')->nullable()->change();
            $table->text('ingredients')->nullable()->change();
            $table->text('instructions')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->string('title')->nullable(false)->change();
            $table->text('summary')->nullable(false)->change();
            $table->text('ingredients')->nullable(false)->change();
            $table->text('instructions')->nullable(false)->change();
        });
    }
};
