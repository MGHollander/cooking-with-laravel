<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'image',
        'servings',
        'preparation_minutes',
        'cooking_minutes',
        'difficulty',
        'summary',
        'instructions',
        'source_label',
        'source_link',
    ];

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::of($value)->slug(),
        );
    }

    public function ingredientsLists()
    {
        return $this->hasMany(IngredientsList::class);
    }
}
