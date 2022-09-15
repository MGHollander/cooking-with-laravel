<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    /**
     * Get the recipe that owns the ingredient.
     */
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
