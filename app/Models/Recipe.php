<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

class Recipe extends Model implements HasMedia, TranslatableContract
{
    use HasFactory, HasTags, InteractsWithMedia, SoftDeletes, Translatable;

    public $translationModel = RecipeTranslation::class;
    protected $translationForeignKey = 'recipe_id';
    protected $localeKey = 'locale';

    public array $translatedAttributes = [
        'slug',
        'title',
        'summary',
        'ingredients',
        'instructions',
    ];

    protected $fillable = [
        'user_id',
        'servings',
        'preparation_minutes',
        'cooking_minutes',
        'difficulty',
        'source_label',
        'source_link',
        'no_index',
    ];

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function primaryTranslation()
    {
        return $this->translations()->first();
    }

    public function primaryLocale(): string
    {
        return $this->primaryTranslation()?->locale ?? config('app.fallback_locale');
    }

    public function getSlugForLocale(?string $locale = null): ?string
    {    
        $locale = $locale ?? $this->primaryLocale();
        return $this->translate($locale)?->slug;
    }

    public function getTitleForLocale(?string $locale = null): string
    {
        $locale = $locale ?? $this->primaryLocale();
        return $this->translate($locale)?->title ?? 'Untitled Recipe';
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

    public function syncTagsInLocale(array $tagNames, string $locale): void
    {
        $tags = collect($tagNames)->map(function (string $tagName) use ($locale) {
            $tag = Tag::findOrCreate($tagName, null, $locale);
            
            if (!$tag->hasTranslation('name', $locale)) {
                $tag->setTranslation('name', $locale, $tagName);
                $tag->save();
            }
            
            return $tag;
        });

        $this->syncTags($tags);
    }

    public function getAllTranslations(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->translations;
    }

    public function hasTranslation(?string $locale = null): bool
    {
        if ($locale === null) {
            $locale = app()->getLocale();
        }
        
        return $this->translations()->where('locale', $locale)->exists();
    }

    public function getAlternateUrls(): array
    {
        $urls = [];
        
        foreach ($this->translations as $translation) {
            $urls[$translation->locale] = route_recipe_show($translation->slug, $translation->locale);
        }
        
        return $urls;
    }
}
