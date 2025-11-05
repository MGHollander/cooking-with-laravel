<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    use HasFactory, HasSlug, HasTags, InteractsWithMedia, SoftDeletes;

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
        'no_index',
    ];

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::of($value)->slug(),
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

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('show')
            ->performOnCollections('recipe_image')
            ->format('webp')
            ->nonQueued();

        $this
            ->addMediaConversion('card')
            ->performOnCollections('recipe_image')
            ->format('webp')
            ->nonQueued();
    }

    public function scopeWithActiveAuthor($query)
    {
        return $query->whereHas('author');
    }
}
