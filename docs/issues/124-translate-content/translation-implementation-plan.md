# Dynamic Content Translation Implementation Plan

**This is the first version. See [translation-implementation-plan.md](docs/issues/124-translate-content/translation-implementation-plan.md) for the latest and implmented plan.**

## Executive Summary

This document outlines the implementation plan for adding dynamic content translation (EN/NL) to the Cooking with Laravel recipe application. We will use the **separate translation tables approach** (Laravel's recommended pattern) with manual translation entry.

## Architecture Overview

### Database Structure

```
┌─────────────────┐         ┌──────────────────────────┐
│    recipes      │         │   recipe_translations    │
├─────────────────┤         ├──────────────────────────┤
│ id              │◄───────┤ recipe_id (FK)           │
│ user_id         │         │ locale (en/nl)           │
│ slug            │         │ title                    │
│ servings        │         │ summary                  │
│ difficulty      │         │ ingredients              │
│ *_minutes       │         │ instructions             │
│ source_*        │         │ created_at               │
│ no_index        │         │ updated_at               │
│ timestamps      │         └──────────────────────────┤
│ soft_deletes    │         UNIQUE(recipe_id, locale)  
└─────────────────┘
```

### Translation Strategy

- **Source Language**: Flexible - recipes can be created in either EN or NL
- **Required Languages**: Both EN and NL translations required for each recipe
- **Fallback**: If translation missing, show available language with indicator
- **Tags**: Also translated using Spatie Tags built-in translation support

---

## Phase 1: Database & Package Setup

### 1.1 Install Required Package

```bash
composer require astrotomic/laravel-translatable
```

**Package**: [astrotomic/laravel-translatable](https://github.com/Astrotomic/laravel-translatable)
- Industry standard for Laravel translations
- Works seamlessly with Eloquent
- Built-in fallback mechanism
- Active maintenance and community support

### 1.2 Create Migration for Translation Table

**File**: `database/migrations/YYYY_MM_DD_create_recipe_translations_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recipe_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->string('locale', 2)->index();
            
            // Translatable fields
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('ingredients');
            $table->text('instructions');
            
            $table->timestamps();
            
            $table->unique(['recipe_id', 'locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipe_translations');
    }
};
```

### 1.3 Update Recipe Table Migration

Remove translatable columns from main `recipes` table:

```php
// Remove these columns (in new migration):
// - title
// - summary
// - ingredients
// - instructions
```

**Note**: We'll create a migration to drop these columns AFTER migrating data in Phase 4.

---

## Phase 2: Model Layer

### 2.1 Create RecipeTranslation Model

**File**: `app/Models/RecipeTranslation.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'summary',
        'ingredients',
        'instructions',
    ];
    
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
```

### 2.2 Update Recipe Model

**File**: `app/Models/Recipe.php`

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
// ... other imports

class Recipe extends Model implements HasMedia, TranslatableContract
{
    use HasFactory, HasSlug, HasTags, InteractsWithMedia, SoftDeletes, Translatable;

    // Define translatable attributes
    public array $translatedAttributes = [
        'title',
        'summary', 
        'ingredients',
        'instructions',
    ];

    protected $fillable = [
        'user_id',
        'slug',
        'tags',
        'servings',
        'preparation_minutes',
        'cooking_minutes',
        'difficulty',
        'source_label',
        'source_link',
        'no_index',
    ];
    
    // Slug should now be generated from translated title
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn($model) => $model->translate('en')->title ?? $model->title)
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }
    
    // Add helper method for getting title in any language
    public function getTitleInLocale(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translate($locale)?->title 
            ?? $this->translate(config('app.fallback_locale'))?->title 
            ?? 'Untitled Recipe';
    }
    
    // Method to check if translation exists for locale
    public function hasTranslation(?string $locale = null): bool
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()
            ->where('locale', $locale)
            ->exists();
    }
}
```

### 2.3 Configuration

**File**: `config/translatable.php` (publish with `php artisan vendor:publish`)

```php
return [
    'locales' => ['en', 'nl'],
    'locale_separator' => '-',
    'fallback_locale' => 'en',
    'use_fallback' => true,
    'use_property_fallback' => true,
];
```

---

## Phase 3: Tags Translation

Spatie Tags has built-in translation support. We just need to enable it.

### 3.1 Update Tag Configuration

**File**: `config/tags.php`

```php
return [
    // ... existing config
    
    // Enable translation
    'translations' => true,
];
```

### 3.2 Create Tag Translations Migration

```bash
php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="migrations"
```

This will create `create_tag_translations_table` migration.

### 3.3 Usage in Recipe Model

```php
// Setting translatable tags
$recipe->syncTagsWithType(['italian', 'pasta'], 'category', 'nl');
$recipe->syncTagsWithType(['italian', 'pasta'], 'category', 'en');

// Getting translated tags
$tags = $recipe->tags; // Returns tags in current locale
```

---

## Phase 4: Data Migration

### 4.1 Create Data Migration Command

**File**: `app/Console/Commands/MigrateRecipeTranslations.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Recipe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateRecipeTranslations extends Command
{
    protected $signature = 'recipes:migrate-translations 
                          {--chunk=50 : Number of recipes to process at once}
                          {--dry-run : Run without making changes}';
    
    protected $description = 'Migrate existing recipe data to translation table';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $chunk = (int) $this->option('chunk');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Starting recipe translation migration...');
        
        DB::transaction(function () use ($dryRun, $chunk) {
            Recipe::withoutGlobalScopes()
                ->chunk($chunk, function ($recipes) use ($dryRun) {
                    foreach ($recipes as $recipe) {
                        // Assume existing data is in English
                        $translationData = [
                            'locale' => 'en',
                            'title' => $recipe->title,
                            'summary' => $recipe->summary,
                            'ingredients' => $recipe->ingredients,
                            'instructions' => $recipe->instructions,
                        ];
                        
                        $this->info("Processing recipe #{$recipe->id}: {$recipe->title}");
                        
                        if (!$dryRun) {
                            // Create English translation
                            $recipe->translations()->updateOrCreate(
                                ['locale' => 'en'],
                                $translationData
                            );
                            
                            // Create empty Dutch translation (to be filled later)
                            $recipe->translations()->updateOrCreate(
                                ['locale' => 'nl'],
                                [
                                    'title' => $recipe->title, // Temporary: use EN title
                                    'summary' => $recipe->summary,
                                    'ingredients' => $recipe->ingredients,
                                    'instructions' => $recipe->instructions,
                                ]
                            );
                        }
                        
                        $this->line("  ✓ Created EN and NL translations");
                    }
                });
        });
        
        $this->info('Migration complete!');
        
        if (!$dryRun) {
            $this->warn('IMPORTANT: You should now manually review and translate NL content.');
            $this->warn('After that, run the migration to drop old columns from recipes table.');
        }
    }
}
```

### 4.2 Run Migration

```bash
# Test first
php artisan recipes:migrate-translations --dry-run

# Run for real
php artisan recipes:migrate-translations

# Verify
php artisan tinker
>>> Recipe::first()->translations()->count()
=> 2
```

### 4.3 Drop Old Columns (After Verification)

**File**: `database/migrations/YYYY_MM_DD_remove_translatable_fields_from_recipes.php`

```php
public function up()
{
    Schema::table('recipes', function (Blueprint $table) {
        $table->dropColumn(['title', 'summary', 'ingredients', 'instructions']);
    });
}
```

**⚠️ IMPORTANT**: Only run this after verifying translations are working correctly!

---

## Phase 5: Backend Updates

### 5.1 Update RecipeRequest Validation

**File**: `app/Http/Requests/Recipe/RecipeRequest.php`

```php
public function rules()
{
    return [
        // Translatable fields for each locale
        'translations' => 'required|array',
        'translations.*.locale' => 'required|in:en,nl',
        'translations.*.title' => 'required|string|max:255',
        'translations.*.summary' => 'nullable|string',
        'translations.*.ingredients' => 'required|string',
        'translations.*.instructions' => 'required|string',
        
        // Tags per locale
        'tags' => 'nullable|array',
        'tags.en' => 'nullable|string',
        'tags.nl' => 'nullable|string',
        
        // Non-translatable fields
        'media' => ['nullable', 'image', 'mimes:' . ImageTypeHelper::getMimes()],
        'servings' => ['required', 'integer', 'min:1'],
        'preparation_minutes' => ['nullable', 'integer', 'min:1'],
        'cooking_minutes' => ['nullable', 'integer', 'min:1'],
        'difficulty' => 'required|in:easy,average,difficult',
        'source_label' => 'nullable|string',
        'source_link' => ['nullable', 'url'],
        'no_index' => ['nullable', 'boolean'],
    ];
}
```

### 5.2 Update RecipeController Store Method

```php
public function store(RecipeRequest $request)
{
    $attributes = $request->validated();
    $attributes['user_id'] = $request->user()->id;
    
    DB::transaction(function () use ($attributes, $request, &$recipe) {
        // Create recipe with non-translatable fields
        $recipe = Recipe::create([
            'user_id' => $attributes['user_id'],
            'servings' => $attributes['servings'],
            'preparation_minutes' => $attributes['preparation_minutes'] ?? null,
            'cooking_minutes' => $attributes['cooking_minutes'] ?? null,
            'difficulty' => $attributes['difficulty'],
            'source_label' => $attributes['source_label'] ?? null,
            'source_link' => $attributes['source_link'] ?? null,
            'no_index' => $attributes['no_index'] ?? false,
        ]);
        
        // Create translations
        foreach ($attributes['translations'] as $translation) {
            $recipe->translations()->create($translation);
        }
        
        // Sync tags for each locale
        if (!empty($attributes['tags'])) {
            foreach ($attributes['tags'] as $locale => $tagString) {
                if ($tagString) {
                    $tags = array_filter(array_map('trim', explode(',', $tagString)));
                    $recipe->syncTagsWithType($tags, 'recipe', $locale);
                }
            }
        }
        
        $this->saveMedia($request, $recipe);
    });
    
    Session::flash('success', __('recipes.messages.created'));
    
    return Inertia::location(route('recipes.edit', $recipe->id));
}
```

### 5.3 Update RecipeController Edit Method

```php
public function edit(Recipe $recipe)
{
    $recipe->load('translations');
    
    $translations = [];
    foreach (['en', 'nl'] as $locale) {
        $translation = $recipe->translations->firstWhere('locale', $locale);
        $translations[$locale] = [
            'title' => $translation?->title ?? '',
            'summary' => $translation?->summary ? strip_tags($translation->summary, '<strong><em><u>') : '',
            'ingredients' => $translation?->ingredients ?? '',
            'instructions' => $translation?->instructions ? strip_tags($translation->instructions, '<strong><em><u><h3><ol><ul><li>') : '',
        ];
    }
    
    $tags = [];
    foreach (['en', 'nl'] as $locale) {
        $tags[$locale] = $recipe->tagsTranslated($locale)->pluck('name')->implode(', ');
    }
    
    return Inertia::render('Recipes/Form', [
        'recipe' => [
            'id' => $recipe->id,
            'slug' => $recipe->slug,
            'translations' => $translations,
            'tags' => $tags,
            'media' => $recipe->getFirstMedia('recipe_image'),
            'servings' => $recipe->servings,
            'preparation_minutes' => $recipe->preparation_minutes,
            'cooking_minutes' => $recipe->cooking_minutes,
            'difficulty' => $recipe->difficulty,
            'source_label' => $recipe->source_label,
            'source_link' => $recipe->source_link,
            'no_index' => $recipe->no_index,
        ],
        'config' => [
            'max_file_size' => config('media-library.max_file_size'),
            'image_dimensions' => config('media-library.image_dimensions.recipe'),
            'supported_mime_types' => ImageTypeHelper::getMimeTypes(),
        ],
    ]);
}
```

### 5.4 Update Show Method

```php
public function show(Request $request, string $slug): JsonResponse|View|Response
{
    $recipe = Recipe::where('slug', $slug)
        ->with(['translations', 'tags'])
        ->whereHas('author')
        ->first();

    if (!$recipe) {
        return $this->notFound($slug);
    }

    $locale = app()->getLocale();
    $translation = $recipe->translate($locale);
    
    // Fallback to English if translation doesn't exist
    if (!$translation) {
        $translation = $recipe->translate('en');
        $showTranslationWarning = true;
    }

    $this->setJsonLdData($recipe);

    return view('kocina.recipes.show', [
        'recipe' => [
            'id' => $recipe->id,
            'author' => $recipe->author->name,
            'user_id' => $recipe->user_id,
            'title' => $translation->title,
            'slug' => $recipe->slug,
            'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
            'summary' => strip_tags($translation->summary, '<strong><em><u>'),
            'tags' => $recipe->tagsTranslated($locale)->pluck('name'),
            'servings' => $recipe->servings,
            'preparation_minutes' => $recipe->preparation_minutes,
            'cooking_minutes' => $recipe->cooking_minutes,
            'difficulty' => Str::ucfirst(__('recipes.'.$recipe->difficulty)),
            'ingredients' => (new IngredientsResource(''))->transformIngredients($translation->ingredients),
            'instructions' => strip_tags($translation->instructions, '<strong><em><u><h3><ol><ul><li>'),
            'source_label' => $recipe->source_label,
            'source_link' => $recipe->source_link,
            'created_at' => $recipe->created_at,
            'no_index' => $recipe->no_index,
            'missing_translation' => $showTranslationWarning ?? false,
        ],
        'open_graph' => [
            'title' => $translation->title,
            'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
            'url' => URL::current(),
        ],
    ]);
}
```

---

## Phase 6: Frontend Forms

### 6.1 Update Recipe Form Component

**File**: `resources/js/Pages/Recipes/Form.vue`

Add language tabs for translation input:

```vue
<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({ 
    recipe: Object, 
    config: Object 
});

const edit = route().current("recipes.edit") ?? false;
const activeTab = ref('en');

// Form structure
const form = useForm({
    _method: edit ? "PATCH" : "POST",
    translations: {
        en: {
            locale: 'en',
            title: edit ? props.recipe.translations?.en?.title ?? '' : '',
            summary: edit ? props.recipe.translations?.en?.summary ?? '' : '',
            ingredients: edit ? props.recipe.translations?.en?.ingredients ?? '' : '',
            instructions: edit ? props.recipe.translations?.en?.instructions ?? '' : '',
        },
        nl: {
            locale: 'nl',
            title: edit ? props.recipe.translations?.nl?.title ?? '' : '',
            summary: edit ? props.recipe.translations?.nl?.summary ?? '' : '',
            ingredients: edit ? props.recipe.translations?.nl?.ingredients ?? '' : '',
            instructions: edit ? props.recipe.translations?.nl?.instructions ?? '' : '',
        },
    },
    tags: {
        en: edit ? props.recipe.tags?.en ?? '' : '',
        nl: edit ? props.recipe.tags?.nl ?? '' : '',
    },
    // ... other fields
});

const save = () => {
    // Convert translations object to array for backend
    const formData = {
        ...form,
        translations: Object.values(form.translations),
    };
    
    form.transform(() => formData)
        .post(edit ? route("recipes.update", props.recipe.id) : route("recipes.store"));
};
</script>

<template>
    <DefaultLayout>
        <template #header>
            {{ title }}
        </template>

        <form class="mx-auto mb-12 max-w-3xl space-y-8" @submit.prevent="save">
            <!-- Language Tabs -->
            <div class="bg-white shadow sm:rounded">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            type="button"
                            @click="activeTab = 'en'"
                            :class="[
                                activeTab === 'en'
                                    ? 'border-indigo-500 text-indigo-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                            ]"
                        >
                            🇬🇧 English
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'nl'"
                            :class="[
                                activeTab === 'nl'
                                    ? 'border-indigo-500 text-indigo-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                            ]"
                        >
                            🇳🇱 Nederlands
                        </button>
                    </nav>
                </div>

                <!-- English Fields -->
                <div v-show="activeTab === 'en'" class="px-6 py-5 space-y-6">
                    <div class="space-y-1">
                        <Label for="title_en" :value="$t('recipes.form.title')" />
                        <Input 
                            v-model="form.translations.en.title" 
                            class="block w-full" 
                            required 
                            type="text" 
                        />
                        <InputError :message="form.errors['translations.0.title']" />
                    </div>

                    <div class="space-y-1">
                        <Label for="summary_en" :value="$t('recipes.form.summary')" />
                        <TipTapEditor
                            v-model="form.translations.en.summary"
                            :placeholder="$t('recipes.form.summary_placeholder')"
                            :rows="4"
                            :toolbar="['bold', 'italic', 'underline']"
                        />
                        <InputError :message="form.errors['translations.0.summary']" />
                    </div>

                    <div class="space-y-1">
                        <Label for="ingredients_en" :value="$t('recipes.form.ingredients')" />
                        <Textarea 
                            v-model="form.translations.en.ingredients" 
                            rows="10" 
                            class="block w-full" 
                            required 
                        />
                        <InputError :message="form.errors['translations.0.ingredients']" />
                    </div>

                    <div class="space-y-1">
                        <Label for="instructions_en" :value="$t('recipes.form.instructions')" />
                        <TipTapEditor
                            v-model="form.translations.en.instructions"
                            :placeholder="$t('recipes.form.instructions_placeholder')"
                            :rows="10"
                            :toolbar="['orderedList', 'bulletList', '|', 'bold', 'italic', 'underline', '|', 'heading']"
                        />
                        <InputError :message="form.errors['translations.0.instructions']" />
                    </div>
                    
                    <div class="space-y-1">
                        <Label for="tags_en" :value="$t('recipes.form.tags')" />
                        <Input v-model="form.tags.en" class="block w-full" type="text" />
                        <InputError :message="form.errors['tags.en']" />
                    </div>
                </div>

                <!-- Dutch Fields -->
                <div v-show="activeTab === 'nl'" class="px-6 py-5 space-y-6">
                    <div class="space-y-1">
                        <Label for="title_nl" :value="$t('recipes.form.title')" />
                        <Input 
                            v-model="form.translations.nl.title" 
                            class="block w-full" 
                            required 
                            type="text" 
                        />
                        <InputError :message="form.errors['translations.1.title']" />
                    </div>

                    <div class="space-y-1">
                        <Label for="summary_nl" :value="$t('recipes.form.summary')" />
                        <TipTapEditor
                            v-model="form.translations.nl.summary"
                            :placeholder="$t('recipes.form.summary_placeholder')"
                            :rows="4"
                            :toolbar="['bold', 'italic', 'underline']"
                        />
                        <InputError :message="form.errors['translations.1.summary']" />
                    </div>

                    <div class="space-y-1">
                        <Label for="ingredients_nl" :value="$t('recipes.form.ingredients')" />
                        <Textarea 
                            v-model="form.translations.nl.ingredients" 
                            rows="10" 
                            class="block w-full" 
                            required 
                        />
                        <InputError :message="form.errors['translations.1.ingredients']" />
                    </div>

                    <div class="space-y-1">
                        <Label for="instructions_nl" :value="$t('recipes.form.instructions')" />
                        <TipTapEditor
                            v-model="form.translations.nl.instructions"
                            :placeholder="$t('recipes.form.instructions_placeholder')"
                            :rows="10"
                            :toolbar="['orderedList', 'bulletList', '|', 'bold', 'italic', 'underline', '|', 'heading']"
                        />
                        <InputError :message="form.errors['translations.1.instructions']" />
                    </div>
                    
                    <div class="space-y-1">
                        <Label for="tags_nl" :value="$t('recipes.form.tags')" />
                        <Input v-model="form.tags.nl" class="block w-full" type="text" />
                        <InputError :message="form.errors['tags.nl']" />
                    </div>
                </div>
            </div>

            <!-- Non-translatable fields (image, servings, etc.) remain the same -->
            <!-- ... rest of form ... -->
        </form>
    </DefaultLayout>
</template>
```

---

## Phase 7: Display Logic

### 7.1 Update Recipe Card Component

**File**: `resources/views/components/kocina/recipe-card.blade.php`

```blade
@props(['recipe'])

<a href="{{ route('recipes.show', $recipe['slug']) }}" class="recipe-card">
    @if($recipe['image'])
        <img src="{{ $recipe['image'] }}" alt="{{ $recipe['title'] }}">
    @endif
    <h3>{{ $recipe['title'] }}</h3>
    
    @if(!empty($recipe['missing_translation']))
        <span class="badge badge-warning">
            {{ __('recipes.translation_missing') }}
        </span>
    @endif
</a>
```

### 7.2 Update Index View

**File**: `app/Http/Controllers/Recipe/RecipeController.php` - `index()` method

```php
public function index(): View
{
    $locale = app()->getLocale();
    
    return view('kocina.recipes.index', [
        'recipes' => Recipe::query()
            ->with(['translations' => function($query) use ($locale) {
                $query->where('locale', $locale)
                    ->orWhere('locale', config('app.fallback_locale'));
            }])
            ->whereHas('author')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->through(function ($recipe) use ($locale) {
                $translation = $recipe->translate($locale) 
                    ?? $recipe->translate(config('app.fallback_locale'));
                
                return [
                    'id' => $recipe->id,
                    'title' => $translation?->title ?? 'Untitled',
                    'slug' => $recipe->slug,
                    'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                    'no_index' => $recipe->no_index,
                    'missing_translation' => !$recipe->hasTranslation($locale),
                ];
            }),
    ]);
}
```

---

## Phase 8: Search & Filtering

### 8.1 Update Search to Include Translations

**File**: `app/Http/Controllers/SearchController.php`

```php
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

public function index(Request $request)
{
    $searchTerm = $request->input('search');
    $locale = app()->getLocale();
    
    $recipes = Search::new()
        ->add(RecipeTranslation::class, ['title', 'ingredients', 'instructions'])
        ->where('locale', $locale)
        ->beginWithWildcard()
        ->paginate(12)
        ->search($searchTerm)
        ->map(function($translation) {
            $recipe = $translation->recipe;
            return [
                'id' => $recipe->id,
                'title' => $translation->title,
                'slug' => $recipe->slug,
                'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                'no_index' => $recipe->no_index,
            ];
        });

    return view('kocina.search.index', [
        'recipes' => $recipes,
        'searchTerm' => $searchTerm,
    ]);
}
```

---

## Phase 9: SEO & Structured Data

### 9.1 Update JSON-LD Data

```php
private function setJsonLdData(Recipe $recipe): void
{
    $locale = app()->getLocale();
    $translation = $recipe->translate($locale) ?? $recipe->translate('en');
    
    JsonLd::setType('Recipe');
    JsonLd::setTitle($translation->title);
    
    if ($translation->summary) {
        JsonLd::setDescription(strip_tags($translation->summary));
    }
    
    JsonLd::addValues([
        'datePublished' => $recipe->created_at,
        'recipeYield' => $recipe->servings,
        'recipeIngredient' => new StructuredDataIngredientsResource($translation->ingredients),
        'recipeInstructions' => new StructuredDataInstructionsResource($translation->instructions),
        'inLanguage' => $locale,
    ]);
    
    // ... rest of SEO data
}
```

### 9.2 Add Alternate Language Links

**File**: `resources/views/kocina/recipes/show.blade.php`

```blade
<head>
    <!-- ... existing meta tags ... -->
    
    <!-- Alternate language versions -->
    <link rel="alternate" hreflang="en" href="{{ route('recipes.show', $recipe['slug']) }}?lang=en" />
    <link rel="alternate" hreflang="nl" href="{{ route('recipes.show', $recipe['slug']) }}?lang=nl" />
</head>
```

---

## Phase 10: Testing & Validation

### 10.1 Create Feature Tests

**File**: `tests/Feature/RecipeTranslationTest.php`

```php
<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_can_be_created_with_translations()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('recipes.store'), [
            'translations' => [
                ['locale' => 'en', 'title' => 'English Title', 'ingredients' => 'Flour', 'instructions' => 'Mix'],
                ['locale' => 'nl', 'title' => 'Nederlandse Titel', 'ingredients' => 'Bloem', 'instructions' => 'Mixen'],
            ],
            'servings' => 4,
            'difficulty' => 'easy',
        ]);
        
        $this->assertDatabaseHas('recipe_translations', [
            'locale' => 'en',
            'title' => 'English Title',
        ]);
        
        $this->assertDatabaseHas('recipe_translations', [
            'locale' => 'nl',
            'title' => 'Nederlandse Titel',
        ]);
    }
    
    public function test_recipe_displays_in_current_locale()
    {
        $recipe = Recipe::factory()
            ->hasTranslations(['en', 'nl'])
            ->create();
        
        app()->setLocale('nl');
        
        $response = $this->get(route('recipes.show', $recipe->slug));
        
        $response->assertSee($recipe->translate('nl')->title);
    }
    
    public function test_recipe_falls_back_to_english_when_translation_missing()
    {
        $recipe = Recipe::factory()
            ->hasTranslations(['en']) // Only English
            ->create();
        
        app()->setLocale('nl');
        
        $response = $this->get(route('recipes.show', $recipe->slug));
        
        // Should show English title with warning
        $response->assertSee($recipe->translate('en')->title);
        $response->assertSee(__('recipes.translation_missing'));
    }
}
```

### 10.2 Testing Checklist

- [ ] Create recipe with both EN and NL translations
- [ ] Edit recipe and update both translations
- [ ] View recipe in EN locale - shows EN content
- [ ] View recipe in NL locale - shows NL content
- [ ] View recipe with missing NL translation - shows EN with warning
- [ ] Search recipes in EN - finds EN content
- [ ] Search recipes in NL - finds NL content
- [ ] Tags display in current locale
- [ ] Recipe import creates EN translation by default
- [ ] Slug generation works with translated titles
- [ ] SEO meta tags show translated content
- [ ] JSON-LD structured data uses correct language

---

## Migration Rollback Strategy

If something goes wrong, you can rollback:

```bash
# Rollback translation tables
php artisan migrate:rollback --step=2

# If data was already migrated, restore from backup
mysql cookbook < backup.sql
```

**Recommended**: Take a full database backup before starting Phase 4.

---

## Estimated Timeline

- **Phase 1-2**: Backend Setup (2-3 hours)
- **Phase 3**: Tags Translation (1 hour)
- **Phase 4**: Data Migration (2-3 hours including verification)
- **Phase 5**: Controller Updates (3-4 hours)
- **Phase 6**: Frontend Forms (4-5 hours)
- **Phase 7**: Display Logic (2-3 hours)
- **Phase 8**: Search Updates (1-2 hours)
- **Phase 9**: SEO Updates (1-2 hours)
- **Phase 10**: Testing (3-4 hours)

**Total: 20-30 hours** (depending on complexity and testing thoroughness)

---

## Next Steps

1. Review this plan and provide feedback
2. Decide on any modifications needed
3. Create a backup of the current database
4. Begin implementation starting with Phase 1

Would you like to proceed with this plan, or do you have questions/modifications?
