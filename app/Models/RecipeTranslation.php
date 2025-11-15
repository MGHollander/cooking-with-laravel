<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class RecipeTranslation extends Model
{
    use HasSlug;

    protected $fillable = [
        'locale',
        'slug',
        'title',
        'summary',
        'ingredients',
        'instructions',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->usingLanguage($this->locale);
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::of($value)->slug(),
        );
    }
}



