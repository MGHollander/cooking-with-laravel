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
        Schema::create('recipe_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2)->index();

            $table->string('slug')->unique();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('ingredients');
            $table->text('instructions');

            $table->timestamps();

            $table->unique(['recipe_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_translations');
    }
};
