<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class RecipeTranslation extends Model
{
    use HasSlug, HasUuids;

    protected $fillable = [
        'locale',
        'slug',
        'title',
        'summary',
        'ingredients',
        'instructions',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $translation) {
            if (! $translation->recipe_uuid && $translation->recipe_id) {
                $translation->recipe_uuid = Recipe::query()->whereKey($translation->recipe_id)->value('uuid');
            }
        });
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->usingLanguage($this->locale)
            ->doNotGenerateSlugsOnUpdate();
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::of($value)->slug(),
        );
    }
}
