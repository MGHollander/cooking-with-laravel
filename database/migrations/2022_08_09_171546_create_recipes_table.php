<?php

use App\Models\User;
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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->smallInteger('preparation_minutes')->unsigned()->nullable();
            $table->smallInteger('cooking_minutes')->unsigned()->nullable();
            $table->smallInteger('servings')->unsigned();
            $table->string('difficulty');
            $table->text('summary')->nullable();
            $table->text('ingredients');
            $table->text('instructions');
            $table->string('source_label')->nullable();
            $table->text('source_link')->nullable();
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
        Schema::dropIfExists('recipes');
    }
};
