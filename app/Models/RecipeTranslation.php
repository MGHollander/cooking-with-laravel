<?php

namespace App\Models;

use App\Traits\HasUuidOrId;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class RecipeTranslation extends Model
{
    use HasSlug, HasUuidOrId, HasVersion7Uuids;

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

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
