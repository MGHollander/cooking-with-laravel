<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'image',
        'preparation_minutes',
        'cooking_minutes',
        'servings',
        'difficulty',
        'summary',
        'instructions',
    ];

    public function ingredientsLists()
    {
        return $this->hasMany(IngredientsList::class);
    }
}
