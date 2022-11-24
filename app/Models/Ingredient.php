<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredients_list_id',
        'name',
        'amount',
        'unit',
    ];

    public function ingredientsList()
    {
        return $this->belongsTo(IngredientsList::class);
    }
}
