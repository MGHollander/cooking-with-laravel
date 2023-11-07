<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;

class Recipe extends Model implements HasMedia
{
    use HasSlug;
    use HasTags;
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
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

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('recipe_image')
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('show')
            ->crop('crop-center', 1024, 768)
            ->performOnCollections('recipe_image')
            ->nonQueued();

        $this
            ->addMediaConversion('card')
            ->crop('crop-center', 1024, 576)
            ->performOnCollections('recipe_image')
            ->nonQueued();
    }
}
