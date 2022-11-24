<?php

use App\Models\IngredientsList;
use App\Models\Recipe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredients_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Recipe::class);
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IngredientsList::class);
            $table->string('name');
            $table->float('amount')->unsigned();
            $table->string('unit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredients');
        Schema::dropIfExists('ingredients_lists');
    }
};
