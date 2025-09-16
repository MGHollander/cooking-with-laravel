<?php

use App\Models\Recipe;
use App\Models\User;
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
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->enum('source', ['structured-data', 'firecrawl', 'open-ai']);
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Recipe::class)->constrained();
            $table->json('parsed_data');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['source', 'created_at']);
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
