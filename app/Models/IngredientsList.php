<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientsList extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'title',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
