<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

class Recipe extends Model
{
    use HasSlug;
    use HasTags;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'image',
        'external_image',
        'tags',
        'summary',
        'servings',
        'preparation_minutes',
        'cooking_minutes',
        'difficulty',
        'ingredients',
        'instructions',
        'source_label',
        'source_link',
    ];

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn($value) => Str::of($value)->slug(),
        );
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }
}
