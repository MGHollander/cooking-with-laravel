# Dynamic Content Translation Implementation Plan (Revised)

## Executive Summary

This document outlines a **phased implementation** for adding dynamic content translation (EN/NL) to the Cooking with Laravel recipe application. We will use the **separate translation tables approach** with a simplified initial phase.

### Implementation Phases

**Phase 1** (This Document): Single language per recipe with language selector
- User chooses ONE language (EN or NL) when creating/editing
- Each recipe exists in one language only
- Simple, clean UX without translation complexity
- Foundation for future multi-language support

**Phase 2** (Future): Add translation management capabilities
- Add translations to existing recipes
- Language switcher for recipes with multiple translations
- Translation workflow and UI

---

## Architecture Overview

### Database Structure

```
┌─────────────────┐         ┌──────────────────────────┐
│    recipes      │         │   recipe_translations    │
├─────────────────┤         ├──────────────────────────┤
│ id              │◄───────┤ recipe_id (FK)           │
│ user_id         │         │ locale (en/nl)           │
│ servings        │         │ slug                     │
│ difficulty      │         │ title                    │
│ *_minutes       │         │ summary                  │
│ source_*        │         │ ingredients              │
│ no_index        │         │ instructions             │
│ timestamps      │         │ created_at               │
│ soft_deletes    │         │ updated_at               │
└─────────────────┘         └──────────────────────────┤
                            UNIQUE(recipe_id, locale)
                            UNIQUE(slug)
```

### Key Design Decisions

✅ **Translatable Slug**: Each language version has its own SEO-friendly slug
- Example: `/recepten/pasta-carbonara` (NL) vs `/recipes/pasta-carbonara` (EN)
- Unique constraint on slug ensures no conflicts
- Better for SEO and user experience

✅ **Single Language Per Recipe (Phase 1)**: Simplified workflow
- Language selector dropdown in form (English/Nederlands)
- Create recipe in chosen language
- Edit recipe in same language
- Adding translations comes in Phase 2

✅ **Language-Agnostic Search (Phase 1)**: Keeps it simple
- Search finds all recipes regardless of language
- Phase 2 will add language filtering if needed

✅ **No Language Tabs**: Clean, focused UX
- User isn't overwhelmed with multiple language fields
- Easier to maintain data quality
- Faster recipe creation

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
- Prepared for Phase 2 multi-language support

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
            $table->string('slug')->unique(); // Each translation has unique slug
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

---

## Phase 2: Model Layer

### 2.1 Create RecipeTranslation Model

**File**: `app/Models/RecipeTranslation.php`

```php
<?php

namespace App\Models;

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
            ->usingLanguage($this->locale); // Use translation's locale for proper character handling
    }
    
    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => Str::of($value)->slug(),
        );
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
    use HasFactory, HasTags, InteractsWithMedia, SoftDeletes, Translatable;
    // Note: Removed HasSlug - now handled by RecipeTranslation

    // Define translatable attributes
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
    
    // Get primary translation (the one that exists for this recipe)
    public function primaryTranslation()
    {
        return $this->translations()->first();
    }
    
    // Get locale of primary translation
    public function primaryLocale(): string
    {
        return $this->primaryTranslation()?->locale ?? config('app.fallback_locale');
    }
    
    // Helper to get slug for current or specified locale
    public function getSlugForLocale(?string $locale = null): ?string
    {
        $locale = $locale ?? $this->primaryLocale();
        return $this->translate($locale)?->slug;
    }
    
    // Helper to get title for current or specified locale
    public function getTitleForLocale(?string $locale = null): string
    {
        $locale = $locale ?? $this->primaryLocale();
        return $this->translate($locale)?->title ?? 'Untitled Recipe';
    }
}
```

### 2.3 Update Configuration

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

For Phase 1, we'll keep tags simple - they stay in the recipe's language.

### 3.1 Keep Tags Simple (No Translation Yet)

Tags will be stored in the recipe's primary language. This keeps the implementation simple for now.

```php
// Setting tags (in recipe's language)
$recipe->syncTags(['italian', 'pasta']);

// Getting tags
$tags = $recipe->tags; // Returns tags
```

**Phase 2 Enhancement**: We can add Spatie Tags translation support later if needed.

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
                          {--locale=en : Default locale for existing recipes}
                          {--chunk=50 : Number of recipes to process at once}
                          {--dry-run : Run without making changes}';
    
    protected $description = 'Migrate existing recipe data to translation table';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $chunk = (int) $this->option('chunk');
        $locale = $this->option('locale');
        
        if (!in_array($locale, ['en', 'nl'])) {
            $this->error('Invalid locale. Must be "en" or "nl".');
            return 1;
        }
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $this->info("Starting recipe translation migration (locale: {$locale})...");
        
        $count = 0;
        
        DB::transaction(function () use ($dryRun, $chunk, $locale, &$count) {
            Recipe::withoutGlobalScopes()
                ->chunk($chunk, function ($recipes) use ($dryRun, $locale, &$count) {
                    foreach ($recipes as $recipe) {
                        $translationData = [
                            'locale' => $locale,
                            'slug' => $recipe->slug,
                            'title' => $recipe->title,
                            'summary' => $recipe->summary,
                            'ingredients' => $recipe->ingredients,
                            'instructions' => $recipe->instructions,
                        ];
                        
                        $this->info("Processing recipe #{$recipe->id}: {$recipe->title}");
                        
                        if (!$dryRun) {
                            $recipe->translations()->updateOrCreate(
                                ['locale' => $locale],
                                $translationData
                            );
                            $count++;
                        }
                        
                        $this->line("  ✓ Created {$locale} translation");
                    }
                });
        });
        
        $this->info("Migration complete! Processed {$count} recipes.");
        
        if (!$dryRun) {
            $this->warn('');
            $this->warn('NEXT STEPS:');
            $this->warn('1. Verify translations in database');
            $this->warn('2. Test recipe viewing and editing');
            $this->warn('3. Run migration to drop old columns from recipes table');
            $this->warn('   php artisan migrate');
        }
        
        return 0;
    }
}
```

### 4.2 Run Migration

```bash
# Test first - check what will happen
php artisan recipes:migrate-translations --dry-run

# Run for real - assume existing recipes are in English
php artisan recipes:migrate-translations --locale=en

# Or if your existing recipes are in Dutch
php artisan recipes:migrate-translations --locale=nl

# Verify
php artisan tinker
>>> Recipe::first()->translations()->count()
=> 1
>>> Recipe::first()->primaryLocale()
=> "en"
```

### 4.3 Drop Old Columns (After Verification)

**Only after verifying everything works:**

**File**: `database/migrations/YYYY_MM_DD_remove_translatable_fields_from_recipes.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropUnique(['slug']); // Remove unique constraint first
            $table->dropColumn(['slug', 'title', 'summary', 'ingredients', 'instructions']);
        });
    }

    public function down()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->text('ingredients');
            $table->text('instructions');
        });
    }
};
```

**⚠️ IMPORTANT**: Only run this AFTER successfully migrating data in Phase 4!

---

## Phase 5: Backend Updates

### 5.1 Update RecipeRequest Validation

**File**: `app/Http/Requests/Recipe/RecipeRequest.php`

```php
public function rules()
{
    return [
        // Language selection
        'locale' => 'required|in:en,nl',
        
        // Translatable fields
        'title' => 'required|string|max:255',
        'summary' => 'nullable|string',
        'ingredients' => 'required|string',
        'instructions' => 'required|string',
        
        // Tags (in recipe's language)
        'tags' => 'nullable|string',
        
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
        
        // Create single translation
        $recipe->translations()->create([
            'locale' => $attributes['locale'],
            'title' => $attributes['title'],
            'summary' => $attributes['summary'],
            'ingredients' => $attributes['ingredients'],
            'instructions' => $attributes['instructions'],
            // Slug will be auto-generated by HasSlug trait
        ]);
        
        // Sync tags
        if (!empty($attributes['tags'])) {
            $tags = array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags']))));
            $recipe->syncTags($tags);
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
    $recipe->load('translations', 'tags');
    $translation = $recipe->primaryTranslation();
    
    return Inertia::render('Recipes/Form', [
        'recipe' => [
            'id' => $recipe->id,
            'locale' => $translation->locale,
            'slug' => $translation->slug,
            'title' => $translation->title,
            'summary' => $translation->summary ? strip_tags($translation->summary, '<strong><em><u>') : '',
            'ingredients' => $translation->ingredients,
            'instructions' => strip_tags($translation->instructions, '<strong><em><u><h3><ol><ul><li>'),
            'tags' => $recipe->tags->pluck('name')->implode(', '),
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

### 5.4 Update RecipeController Update Method

```php
public function update(RecipeRequest $request, Recipe $recipe)
{
    $attributes = $request->validated();
    
    DB::transaction(function () use ($attributes, $request, $recipe) {
        // Update non-translatable fields
        $recipe->update([
            'servings' => $attributes['servings'],
            'preparation_minutes' => $attributes['preparation_minutes'] ?? null,
            'cooking_minutes' => $attributes['cooking_minutes'] ?? null,
            'difficulty' => $attributes['difficulty'],
            'source_label' => $attributes['source_label'] ?? null,
            'source_link' => $attributes['source_link'] ?? null,
            'no_index' => $attributes['no_index'] ?? false,
        ]);
        
        // Update translation
        $recipe->translations()->updateOrCreate(
            ['locale' => $attributes['locale']],
            [
                'title' => $attributes['title'],
                'summary' => $attributes['summary'],
                'ingredients' => $attributes['ingredients'],
                'instructions' => $attributes['instructions'],
                // Slug will be auto-updated if title changed
            ]
        );
        
        // Update tags
        if (!empty($attributes['tags'])) {
            $tags = array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags']))));
            $recipe->syncTags($tags);
        } else {
            $recipe->detachTags($recipe->tags);
        }
        
        if ($request->get('destroy_media', false)) {
            $recipe->clearMediaCollection('recipe_image');
        }
        
        $this->saveMedia($request, $recipe);
    });
    
    Session::flash('success', __('recipes.messages.updated'));
    
    // Use the translation's slug
    $slug = $recipe->getSlugForLocale($attributes['locale']);
    return Inertia::location(route('recipes.show', $slug));
}
```

### 5.5 Update RecipeController Show Method

```php
public function show(Request $request, string $slug): JsonResponse|View|Response
{
    // Find by slug in translations table
    $translation = RecipeTranslation::where('slug', $slug)
        ->with('recipe.author', 'recipe.tags')
        ->first();

    if (!$translation || !$translation->recipe->author) {
        return $this->notFound($slug);
    }
    
    $recipe = $translation->recipe;
    
    $this->setJsonLdData($recipe, $translation);

    return view('kocina.recipes.show', [
        'recipe' => [
            'id' => $recipe->id,
            'author' => $recipe->author->name,
            'user_id' => $recipe->user_id,
            'locale' => $translation->locale,
            'title' => $translation->title,
            'slug' => $translation->slug,
            'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
            'summary' => strip_tags($translation->summary, '<strong><em><u>'),
            'tags' => $recipe->tags->pluck('name'),
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
        ],
        'open_graph' => [
            'title' => $translation->title,
            'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
            'url' => URL::current(),
        ],
    ]);
}
```

### 5.6 Update RecipeController Index Method

```php
public function index(): View
{
    return view('kocina.recipes.index', [
        'recipes' => Recipe::query()
            ->with(['translations', 'media'])
            ->whereHas('author')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->through(function ($recipe) {
                $translation = $recipe->primaryTranslation();
                
                return [
                    'id' => $recipe->id,
                    'title' => $translation->title,
                    'slug' => $translation->slug,
                    'locale' => $translation->locale,
                    'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                    'no_index' => $recipe->no_index,
                ];
            }),
    ]);
}
```

### 5.7 Update Import Flow

The import functionality needs to be updated to create recipe translations using the current app locale.

#### 5.7.1 Update ImportController::parseAndPrepareRecipeData

**File**: `app/Http/Controllers/Recipe/ImportController.php`

Add locale to the response data in both return statements:

```php
private function parseAndPrepareRecipeData(string $cleanUrl, string $parser, bool $forceImport, $user): array
{
    // ... existing code ...
    
    // In the first return (reusing existing import):
    return [
        'recipe' => $recipe,
        'import_log_id' => $importLog->id,
        'locale' => app()->getLocale(), // Add this line
        'config' => [
            'image_dimensions' => config('media-library.image_dimensions.recipe'),
            'supported_mime_types' => ImageTypeHelper::getMimeTypes(),
        ],
    ];
    
    // ... existing code ...
    
    // In the second return (new API parsing):
    return [
        'recipe' => $recipe,
        'locale' => app()->getLocale(), // Add this line
    ];
}
```

#### 5.7.2 Update ImportController::store

**File**: `app/Http/Controllers/Recipe/ImportController.php`

Replace the current `store()` method to create translations like `RecipeController::store()`:

```php
public function store(RecipeRequest $request)
{
    $attributes = $request->validated();
    $attributes['user_id'] = $request->user()->id;
    
    // Use app locale if not provided (for backward compatibility)
    $locale = $attributes['locale'] ?? app()->getLocale();
    
    DB::transaction(function () use ($attributes, $request, $locale, &$recipe) {
        // Create recipe with non-translatable fields
        $recipe = Recipe::create([
            'user_id' => $attributes['user_id'],
            'servings' => $attributes['servings'],
            'preparation_minutes' => $attributes['preparation_minutes'] ?? null,
            'cooking_minutes' => $attributes['cooking_minutes'] ?? null,
            'difficulty' => $attributes['difficulty'],
            'source_label' => $attributes['source_label'] ?? null,
            'source_link' => $attributes['source_link'] ?? null,
            'no_index' => $attributes['no_index'] ?? true, // Default to true for imports
        ]);
        
        // Create translation
        $recipe->translations()->create([
            'locale' => $locale,
            'title' => $attributes['title'],
            'summary' => $attributes['summary'],
            'ingredients' => $attributes['ingredients'],
            'instructions' => $attributes['instructions'],
            // Slug will be auto-generated by HasSlug trait
        ]);
        
        // Sync tags
        if (!empty($attributes['tags'])) {
            $tags = array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags']))));
            $recipe->syncTags($tags);
        }
        
        // Handle external image
        if ($externalImage = $request->get('external_image')) {
            $mediaDimensions = $request->get('media_dimensions', []);
            $manipulations = $this->buildManipulations($mediaDimensions);
            
            try {
                $recipe->addMediaFromUrl($externalImage)
                    ->withManipulations($manipulations)
                    ->toMediaCollection('recipe_image');
            } catch (\Exception $e) {
                Log::error('Failed to save the recipe image from url', [
                    'recipe_id' => $recipe->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        // Update import log with created recipe if this was imported from a URL
        if ($importLogId = $request->get('import_log_id')) {
            try {
                $importLog = ImportLog::find($importLogId);
                if ($importLog) {
                    $this->importLogService->updateImportLogWithRecipe($importLog, $recipe);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to update import log with recipe', [
                    'recipe_id' => $recipe->id,
                    'import_log_id' => $importLogId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    });
    
    if ($request->get('return_to_import_page')) {
        $slug = $recipe->getSlugForLocale($locale);
        return redirect()->route('import.index')->with('success', 'Het recept "<a href="'.route('recipes.show', $slug).'"><i>'.$recipe->getTitleForLocale($locale).'</i></a>" is succesvol geïmporteerd! 🎉');
    }
    
    Session::flash('success', 'Het recept is succesvol geïmporteerd! 🎉');
    
    $slug = $recipe->getSlugForLocale($locale);
    return Inertia::location(route('recipes.show', $slug));
}
```

**Note**: Add `use Illuminate\Support\Facades\DB;` at the top of the file if not already present.

---

## Phase 6: Frontend Forms

### 6.1 Update Recipe Form Component

**File**: `resources/js/Pages/Recipes/Form.vue`

Add a simple language selector (no tabs):

```vue
<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({ 
    recipe: Object, 
    config: Object 
});

const edit = route().current("recipes.edit") ?? false;

// Form structure - simplified, single language
const form = useForm({
    _method: edit ? "PATCH" : "POST",
    locale: edit ? props.recipe.locale : 'en',
    title: edit ? props.recipe.title : '',
    summary: edit ? props.recipe.summary : '',
    ingredients: edit ? props.recipe.ingredients : '',
    instructions: edit ? props.recipe.instructions : '',
    tags: edit ? props.recipe.tags : '',
    media: null,
    media_dimensions: null,
    destroy_media: false,
    servings: edit ? props.recipe.servings.toString() : '',
    preparation_minutes: edit ? props.recipe.preparation_minutes?.toString() : '',
    cooking_minutes: edit ? props.recipe.cooking_minutes?.toString() : '',
    difficulty: edit ? props.recipe.difficulty : 'easy',
    source_label: edit ? props.recipe.source_label : '',
    source_link: edit ? props.recipe.source_link : '',
    no_index: edit ? Boolean(props.recipe.no_index) : false,
});

const title = computed(() => 
    edit 
        ? trans('recipes.form.edit_title', { title: form.title }) 
        : trans('recipes.form.create_title')
);

const localeLabel = computed(() => {
    return form.locale === 'en' ? 'English' : 'Nederlands';
});

// ... rest of the component (image handling, save function, etc.)
</script>

<template>
    <Head :title="title" />

    <DefaultLayout>
        <template #header>
            {{ title }}
            <a v-if="edit" :href="route('recipes.show', props.recipe.slug)" class="ml-4 text-sm">
                {{ $t('recipes.form.view_recipe') }}
            </a>
        </template>

        <form class="mx-auto mb-12 max-w-3xl space-y-8" @submit.prevent="save">
            <!-- Language Selector -->
            <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded sm:p-6">
                <div class="space-y-1">
                    <Label for="locale" :value="$t('recipes.form.language')" />
                    <select
                        v-model="form.locale"
                        :disabled="edit"
                        class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <option value="en">🇬🇧 English</option>
                        <option value="nl">🇳🇱 Nederlands</option>
                    </select>
                    <p v-if="edit" class="text-xs text-gray-500">
                        {{ $t('recipes.form.language_locked') }}
                    </p>
                    <InputError :message="form.errors.locale" />
                </div>
            </div>

            <!-- Content Fields -->
            <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded sm:p-6 sm:pb-8">
                <div class="grid grid-cols-12 gap-6">
                    <ValidationErrors class="col-span-12 -mx-4 -mt-5 p-4 sm:-mx-6 sm:-mt-6 sm:rounded-t" />

                    <div class="col-span-12 space-y-1">
                        <Label for="title" :value="$t('recipes.form.title')" />
                        <Input 
                            v-model="form.title" 
                            autocomplete="title" 
                            class="block w-full" 
                            required 
                            type="text" 
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div v-if="edit" class="col-span-12 space-y-1">
                        <Label for="slug" value="Slug" />
                        <blockquote class="font-mono text-sm">/{{ props.recipe.slug }}</blockquote>
                    </div>

                    <!-- Image upload section (same as before) -->
                    <!-- ... -->

                    <div class="col-span-12 space-y-1">
                        <Label for="summary" :value="$t('recipes.form.summary')" />
                        <TipTapEditor
                            v-model="form.summary"
                            :placeholder="$t('recipes.form.summary_placeholder')"
                            :rows="4"
                            :toolbar="['bold', 'italic', 'underline']"
                        />
                        <InputError :message="form.errors.summary" />
                    </div>

                    <div class="col-span-12 space-y-1">
                        <Label for="tags" :value="$t('recipes.form.tags')" />
                        <Input v-model="form.tags" class="block w-full" type="text" />
                        <p class="text-xs text-gray-500">
                            {{ $t('recipes.form.tags_help') }}
                        </p>
                        <InputError :message="form.errors.tags" />
                    </div>
                </div>
            </div>

            <!-- Cooking Details -->
            <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-md sm:p-6">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 space-y-1 self-end sm:col-span-4">
                        <Label for="servings" :value="$t('recipes.form.servings')" />
                        <Input v-model="form.servings" class="block w-full" min="1" required type="number" />
                        <InputError :message="form.errors.servings" />
                    </div>

                    <!-- preparation_minutes, cooking_minutes, difficulty -->
                    <!-- ... same as before ... -->

                    <div class="col-span-12 grid grid-cols-12 gap-6">
                        <div class="col-span-12 space-y-1">
                            <Label>{{ $t('recipes.form.ingredients') }}</Label>
                            <Textarea v-model="form.ingredients" rows="10" class="block w-full" required />
                            <InputError :message="form.errors.ingredients" />
                            <p class="!my-3 text-xs text-gray-500">{{ $t('recipes.form.ingredients_help') }}</p>
                            <ul class="list-outside pl-4 text-xs text-gray-500" v-html="$t('recipes.form.ingredients_help_text')" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="space-y-2 bg-white px-4 py-5 shadow sm:rounded-md sm:p-6">
                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 space-y-1">
                        <Label for="instructions" :value="$t('recipes.form.instructions')" />
                        <TipTapEditor
                            v-model="form.instructions"
                            :placeholder="$t('recipes.form.instructions_placeholder')"
                            :rows="10"
                            :toolbar="['orderedList', 'bulletList', '|', 'bold', 'italic', 'underline', '|', 'heading']"
                        />
                        <InputError :message="form.errors.instructions" />
                    </div>

                    <!-- source_label, source_link, no_index -->
                    <!-- ... same as before ... -->
                </div>
            </div>

            <!-- Fixed bottom bar with Save button -->
            <!-- ... same as before ... -->
        </form>
    </DefaultLayout>
</template>
```

### 6.2 Add Translation Strings

**File**: `lang/en/recipes.php`

```php
'form' => [
    'language' => 'Language',
    'language_locked' => 'Language cannot be changed after creation. To add translations, use the translation feature (coming soon).',
    // ... other strings
],
```

**File**: `lang/nl/recipes.php`

```php
'form' => [
    'language' => 'Taal',
    'language_locked' => 'Taal kan niet worden gewijzigd na het aanmaken. Gebruik de vertaalfunctie om vertalingen toe te voegen (binnenkort beschikbaar).',
    // ... other strings
],
```

#### 6.3 Update Import Form Component

**File**: `resources/js/Pages/Import/Form.vue`

Add locale field to the form (set from API response, not user-selectable):

```vue
<script setup>
// ... existing imports ...

const form = useForm({
  locale: '', // Add this field
  title: "",
  external_image: "",
  // ... rest of existing fields ...
});

// ... existing code ...

onMounted(() => {
  if (isLoading.value) {
    axios
      .post(route("import.import-recipe"), {
        url: props.url,
        parser: props.parser,
        force_import: props.force_import,
      })
      .then((response) => {
        const recipe = response.data.recipe;
        
        form.locale = response.data.locale || 'nl'; // Add this line - use app locale from response
        form.title = recipe.title;
        // ... rest of existing assignments ...
      })
      .catch((error) => {
        router.get(route("import.index"));
      });
  }
});
</script>

<template>
  <!-- No locale selector needed in template - it's set automatically from app locale -->
  <!-- ... rest of existing template ... -->
</template>
```

---

## Phase 7: Display Logic

### 7.1 Update Recipe Card Component

**File**: `resources/views/components/kocina/recipe-card.blade.php`

Add language indicator:

```blade
@props(['recipe'])

<a href="{{ route('recipes.show', $recipe['slug']) }}" class="recipe-card">
    @if($recipe['image'])
        <img src="{{ $recipe['image'] }}" alt="{{ $recipe['title'] }}">
    @endif
    <h3>{{ $recipe['title'] }}</h3>
    
    <!-- Optional: Show language badge -->
    <span class="badge badge-language">
        {{ $recipe['locale'] === 'en' ? '🇬🇧' : '🇳🇱' }}
    </span>
</a>
```

---

## Phase 8: Search (Language-Agnostic)

### 8.1 Keep Search Simple

**File**: `app/Http/Controllers/SearchController.php`

Search finds all recipes regardless of language:

```php
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

public function index(Request $request)
{
    $searchTerm = $request->input('search');
    
    // Search across all recipe translations
    $recipes = Search::new()
        ->add(RecipeTranslation::class, ['title', 'ingredients', 'instructions'])
        ->beginWithWildcard()
        ->paginate(12)
        ->search($searchTerm)
        ->map(function($translation) {
            $recipe = $translation->recipe;
            return [
                'id' => $recipe->id,
                'title' => $translation->title,
                'slug' => $translation->slug,
                'locale' => $translation->locale,
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

**Phase 2 Enhancement**: Add language filter to search if needed.

---

## Phase 9: SEO & Structured Data

### 9.1 Update JSON-LD Data

```php
private function setJsonLdData(Recipe $recipe, RecipeTranslation $translation): void
{
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
        'inLanguage' => $translation->locale,
    ]);
    
    $image = $recipe->getFirstMediaUrl('recipe_image', 'show');
    if ($image) {
        JsonLd::addImage($image);
    }
    
    // ... rest of SEO data (times, tags, etc.)
}
```

### 9.2 Update Routes (if needed)

Consider having language-specific route prefixes:

**File**: `routes/web.php`

```php
// English routes
Route::prefix('recipes')->group(function () {
    Route::get('/{slug}', [RecipeController::class, 'show'])
        ->name('recipes.show')
        ->where('slug', '[a-z0-9-]+');
});

// Dutch routes  
Route::prefix('recepten')->group(function () {
    Route::get('/{slug}', [RecipeController::class, 'show'])
        ->where('slug', '[a-z0-9-]+');
});
```

Or keep it simple with a single route that handles both languages based on slug.

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
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class RecipeTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_recipe_can_be_created_in_english()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('recipes.store'), [
            'locale' => 'en',
            'title' => 'English Recipe',
            'ingredients' => 'Flour, Eggs',
            'instructions' => 'Mix everything',
            'servings' => 4,
            'difficulty' => 'easy',
        ]);
        
        $this->assertDatabaseHas('recipe_translations', [
            'locale' => 'en',
            'title' => 'English Recipe',
        ]);
    }
    
    public function test_recipe_can_be_created_in_dutch()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->post(route('recipes.store'), [
            'locale' => 'nl',
            'title' => 'Nederlands Recept',
            'ingredients' => 'Bloem, Eieren',
            'instructions' => 'Alles mengen',
            'servings' => 4,
            'difficulty' => 'easy',
        ]);
        
        $this->assertDatabaseHas('recipe_translations', [
            'locale' => 'nl',
            'title' => 'Nederlands Recept',
        ]);
    }
    
    public function test_recipe_slug_is_unique_per_translation()
    {
        $user = User::factory()->create();
        
        // Create recipe in English
        $recipe1 = Recipe::factory()->create();
        $recipe1->translations()->create([
            'locale' => 'en',
            'title' => 'Pasta Carbonara',
            'slug' => 'pasta-carbonara',
            'ingredients' => 'test',
            'instructions' => 'test',
        ]);
        
        // Create recipe in Dutch with same title (different slug)
        $recipe2 = Recipe::factory()->create();
        $recipe2->translations()->create([
            'locale' => 'nl',
            'title' => 'Pasta Carbonara',
            'slug' => 'pasta-carbonara',  // This should fail unique constraint
            'ingredients' => 'test',
            'instructions' => 'test',
        ]);
        
        // Should auto-generate unique slug like 'pasta-carbonara-1'
        $this->assertNotEquals(
            $recipe1->getSlugForLocale('en'),
            $recipe2->getSlugForLocale('nl')
        );
    }
    
    public function test_recipe_displays_correct_slug()
    {
        $recipe = Recipe::factory()->create();
        $translation = $recipe->translations()->create([
            'locale' => 'en',
            'title' => 'Test Recipe',
            'slug' => 'test-recipe',
            'ingredients' => 'test',
            'instructions' => 'test',
        ]);
        
        $response = $this->get(route('recipes.show', 'test-recipe'));
        
        $response->assertStatus(200);
        $response->assertSee('Test Recipe');
    }
    
    public function test_imported_recipe_uses_app_locale()
    {
        $user = User::factory()->create();
        
        // Set app locale to Dutch
        App::setLocale('nl');
        
        $response = $this->actingAs($user)->post(route('import.store'), [
            'locale' => app()->getLocale(), // Should be 'nl'
            'title' => 'Geïmporteerd Recept',
            'ingredients' => 'Bloem, Eieren',
            'instructions' => 'Alles mengen',
            'servings' => 4,
            'difficulty' => 'easy',
        ]);
        
        $this->assertDatabaseHas('recipe_translations', [
            'locale' => 'nl',
            'title' => 'Geïmporteerd Recept',
        ]);
        
        $recipe = Recipe::latest()->first();
        $this->assertEquals('nl', $recipe->primaryLocale());
    }
}
```

### 10.2 Testing Checklist

- [ ] Create recipe in English - verifies EN creation
- [ ] Create recipe in Dutch - verifies NL creation
- [ ] Edit recipe - updates in same language
- [ ] View recipe by slug - displays correctly
- [ ] Slug is unique across all languages
- [ ] Search finds recipes in both languages
- [ ] Tags work correctly
- [ ] Recipe import creates translation using app locale
- [ ] Imported recipe uses correct locale from app context
- [ ] Imported recipe slug is generated correctly
- [ ] SEO meta tags show correct language
- [ ] JSON-LD structured data includes inLanguage

---

## Migration Rollback Strategy

If something goes wrong:

```bash
# Rollback translation tables
php artisan migrate:rollback --step=2

# If data was already migrated, restore from backup
mysql cookbook < backup.sql
```

**Recommended**: Take a full database backup before starting Phase 4.

---

## Estimated Timeline (Revised)

- **Phase 1-2**: Backend Setup (2 hours)
- **Phase 3**: Tags (Simple, 30 mins)
- **Phase 4**: Data Migration (2 hours including verification)
- **Phase 5**: Controller Updates (3.5 hours - includes import flow)
- **Phase 6**: Frontend Forms (2 hours - simpler without tabs)
- **Phase 7**: Display Logic (1 hour)
- **Phase 8**: Search (30 mins - simple)
- **Phase 9**: SEO Updates (1 hour)
- **Phase 10**: Testing (2 hours)

**Total: 14-16 hours** (much faster than original plan!)

---

## Future Enhancements (Phase 2)

Once Phase 1 is stable, we can add:

1. **Translation Management UI**
   - "Add Translation" button on recipe edit page
   - Side-by-side translation editor
   - Translation status indicators

2. **Advanced Search**
   - Filter by language
   - Search across all languages with language indicator

3. **Spatie Tags Translation**
   - Translate tag names per language
   - Language-specific tag suggestions

4. **Bulk Operations**
   - Mark recipes for translation
   - Translation workflow/queue
   - Translation progress tracking

---

## Next Steps

1. Review this revised plan
2. Provide any additional feedback
3. Create database backup
4. Begin implementation starting with Phase 1

This phased approach gives you a working translation system quickly, with room to grow!
