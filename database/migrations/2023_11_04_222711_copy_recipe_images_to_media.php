<?php

use App\Models\Recipe;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Recipe::all()->each(function (Recipe $recipe) {
            if ($recipe->image) {
                $recipe
                    ->addMediaFromDisk($recipe->image, 'public')
                    ->toMediaCollection('recipe_image');
            }
        });
    }

};
