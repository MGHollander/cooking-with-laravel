<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Resources\Recipe\IngredientsResource;
use App\Http\Resources\StructuredData\Recipe\IngredientsResource as StructuredDataIngredientsResource;
use App\Http\Resources\StructuredData\Recipe\InstructionsResource as StructuredDataInstructionsResource;
use App\Http\Traits\FillableAttributes;
use App\Models\Recipe;
use App\Models\RecipeTranslation;
use App\Support\ImageTypeHelper;
use Artesaos\SEOTools\Facades\JsonLd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Inertia\Inertia;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;
use Symfony\Component\HttpFoundation\Response;

class RecipeController extends Controller
{
    use FillableAttributes;

    /**
     * Display a listing of the resource.
     */
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
                        'slug' => $recipe->getSlugForLocale($translation->locale),
                        'locale' => $translation->locale,
                        'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                        'no_index' => $recipe->no_index,
                    ];
                }),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Recipes/Form', [
            'config' => [
                'max_file_size' => config('media-library.max_file_size'),
                'image_dimensions' => config('media-library.image_dimensions.recipe'),
                'supported_mime_types' => ImageTypeHelper::getMimeTypes(),
            ],
            'languages' => \App\Support\LanguageHelper::getAllLanguagesWithTranslation(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(RecipeRequest $request)
    {
        $attributes = $request->validated();
        $attributes['user_id'] = $request->user()->id;

        DB::transaction(function () use ($attributes, $request, &$recipe) {
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

            $recipe->translations()->create([
                'locale' => $attributes['locale'],
                'title' => $attributes['title'],
                'summary' => $attributes['summary'] ?? null,
                'ingredients' => $attributes['ingredients'],
                'instructions' => $attributes['instructions'],
            ]);

            if (! empty($attributes['tags'])) {
                $tags = array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags']))));
                $recipe->syncTagsInLocale($tags, $attributes['locale']);
            }

            $this->saveMedia($request, $recipe);
        });

        Session::flash('success', __('recipes.flash.created'));

        return Inertia::location(route('recipes.edit', $recipe->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug): Response|View|RedirectResponse
    {
        [$recipe, $translation] = $this->findRecipeBySlug($slug);

        if ($translation && ! $recipe) {
            $recipe = $translation->recipe;

            return redirect()->route(
                $translation->locale === 'nl' ? 'recipes.show.nl' : 'recipes.show.en',
                $recipe->getSlugForLocale($translation->locale),
                301
            );
        }

        if (! $recipe) {
            return $this->notFound($slug);
        }

        $this->setJsonLdData($recipe, $translation);

        $alternateUrls = $recipe->getAlternateUrls();
        $canonicalUrl = route_recipe_show($recipe->getSlugForLocale($translation->locale), $translation->locale);
        $ogLocale = $this->formatOpenGraphLocale($translation->locale);

        return view('kocina.recipes.show', [
            'recipe' => [
                'id' => $recipe->id,
                'author' => $recipe->author->name,
                'user_id' => $recipe->user_id,
                'locale' => $translation->locale,
                'title' => $translation->title,
                'slug' => $recipe->getSlugForLocale($translation->locale),
                'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
                'summary' => strip_tags($translation->summary, '<strong><em><u>'),
                'tags' => $recipe->tags->map(fn ($tag) => $tag->getTranslation('name', $translation->locale))->filter(),
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
                'description' => $translation->summary ? strip_tags($translation->summary) : null,
                'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
                'url' => URL::current(),
                'type' => 'article',
                'locale' => $ogLocale,
            ],
            'alternate_urls' => $alternateUrls,
            'canonical_url' => $canonicalUrl,
        ]);
    }

    private function findRecipeBySlug(string $slug): array
    {
        $parts = explode('-', $slug);
        $publicId = end($parts);
        $recipe = Recipe::where('public_id', $publicId)->with('author', 'tags')->first();
        $translation = $recipe?->primaryTranslation() ?? null;

        if (! $recipe) {
            $translation = RecipeTranslation::where('slug', $slug)
                ->with('recipe.author', 'recipe.tags')
                ->first();
        }

        return [$recipe, $translation];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Inertia\Response
     */
    public function edit(Recipe $recipe)
    {
        $recipe->load('translations', 'tags');
        $translation = $recipe->primaryTranslation();

        return Inertia::render('Recipes/Form', [
            'recipe' => [
                'id' => $recipe->id,
                'locale' => $translation->locale,
                'slug' => $recipe->getSlugForLocale($translation->locale),
                'title' => $translation->title,
                'summary' => $translation->summary ? strip_tags($translation->summary, '<strong><em><u>') : '',
                'ingredients' => $translation->ingredients,
                'instructions' => strip_tags($translation->instructions, '<strong><em><u><h3><ol><ul><li>'),
                'tags' => $recipe->tags->map(fn ($tag) => $tag->getTranslation('name', $translation->locale))->filter()->implode(', '),
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
            'languages' => \App\Support\LanguageHelper::getAllLanguagesWithTranslation(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function update(RecipeRequest $request, Recipe $recipe)
    {
        $attributes = $request->validated();

        DB::transaction(function () use ($attributes, $request, $recipe) {
            $recipe->update([
                'servings' => $attributes['servings'],
                'preparation_minutes' => $attributes['preparation_minutes'] ?? null,
                'cooking_minutes' => $attributes['cooking_minutes'] ?? null,
                'difficulty' => $attributes['difficulty'],
                'source_label' => $attributes['source_label'] ?? null,
                'source_link' => $attributes['source_link'] ?? null,
                'no_index' => $attributes['no_index'] ?? false,
            ]);

            $currentTranslation = $recipe->primaryTranslation();
            $translationForNewLocale = $recipe->translations()->where('locale', $attributes['locale'])->first();

            if (! $currentTranslation) {
                $recipe->translations()->create([
                    'locale' => $attributes['locale'],
                    'title' => $attributes['title'],
                    'summary' => $attributes['summary'] ?? null,
                    'ingredients' => $attributes['ingredients'],
                    'instructions' => $attributes['instructions'],
                ]);
            } elseif ($translationForNewLocale && $translationForNewLocale->id !== $currentTranslation->id) {
                $translationForNewLocale->update([
                    'title' => $attributes['title'],
                    'summary' => $attributes['summary'] ?? null,
                    'ingredients' => $attributes['ingredients'],
                    'instructions' => $attributes['instructions'],
                ]);
            } elseif ($currentTranslation->locale !== $attributes['locale']) {
                $currentTranslation->update([
                    'locale' => $attributes['locale'],
                    'title' => $attributes['title'],
                    'summary' => $attributes['summary'] ?? null,
                    'ingredients' => $attributes['ingredients'],
                    'instructions' => $attributes['instructions'],
                ]);
            } else {
                $currentTranslation->update([
                    'title' => $attributes['title'],
                    'summary' => $attributes['summary'] ?? null,
                    'ingredients' => $attributes['ingredients'],
                    'instructions' => $attributes['instructions'],
                ]);
            }

            if (! empty($attributes['tags'])) {
                $tags = array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags']))));
                $recipe->syncTagsInLocale($tags, $attributes['locale']);
            } else {
                $recipe->detachTags($recipe->tags);
            }

            if ($request->get('destroy_media', false)) {
                $recipe->clearMediaCollection('recipe_image');
            }

            $this->saveMedia($request, $recipe);
        });

        Session::flash('success', __('recipes.flash.updated'));

        $slug = $recipe->getSlugForLocale($attributes['locale']);

        return Inertia::location(route_recipe_show($slug, $attributes['locale']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->load('translations');
        $translation = $recipe->primaryTranslation();

        $userId = auth()->id();
        $recipeId = $recipe->id;

        $recipe->deletePreservingMedia();

        Log::info("Recipe {$recipeId} deleted by user {$userId}");

        Session::flash('success', __('recipes.flash.deleted', ['title' => $translation->title]));

        return Inertia::location(route('home'));
    }

    private function notFound($slug): \Illuminate\Http\Response
    {
        $parts = explode('-', $slug);
        $potentialPublicId = end($parts);

        // Check if last part matches public_id format
        if (strlen($potentialPublicId) === 15 && preg_match('/^[0-9a-z]+$/', $potentialPublicId)) {
            // Remove public_id from search
            array_pop($parts);
            $searchKey = implode(' ', $parts);
        } else {
            $searchKey = str_replace('-', ' ', $slug);
        }

        $paginator = Search::add(RecipeTranslation::with('recipe.media'), ['title', 'ingredients', 'instructions'])
            ->add(Recipe::with('translations', 'tags'), ['tags.name'])
            ->paginate(12)
            ->beginWithWildcard()
            ->search($searchKey)
            ->withQueryString();

        $recipes = $paginator->setCollection(
            $paginator->getCollection()->map(function ($result) {
                if ($result instanceof RecipeTranslation) {
                    $recipe = $result->recipe;

                    return [
                        'id' => $recipe->id,
                        'title' => $result->title,
                        'slug' => $recipe->getSlugForLocale($result->locale),
                        'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                        'no_index' => $recipe->no_index,
                    ];
                }

                $translation = $result->primaryTranslation();

                return [
                    'id' => $result->id,
                    'title' => $translation->title,
                    'slug' => $result->getSlugForLocale($translation->locale),
                    'image' => $result->getFirstMediaUrl('recipe_image', 'card'),
                    'no_index' => $result->no_index,
                ];
            })
        );

        return response()->view(
            'kocina.recipes.not-found',
            [
                'recipes' => $recipes,
                'searchWords' => explode(' ', $searchKey),
            ],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     */
    private function saveMedia(Request $request, Recipe $recipe): void
    {
        $mediaDimensions = $request->get('media_dimensions', []);

        if (! empty($mediaDimensions['card'])) {
            $cardDimensions = $mediaDimensions['card'];
            $manipulationsCard = [
                'manualCrop' => [
                    (int) $cardDimensions['width'],
                    (int) $cardDimensions['height'],
                    (int) $cardDimensions['left'],
                    (int) $cardDimensions['top'],
                ],
                'width' => [config('media-library.image_dimensions.recipe.conversions.card.width')],
            ];
        }

        if (! empty($mediaDimensions['show'])) {
            $showDimensions = $mediaDimensions['show'];
            $manipulationsShow = [
                'manualCrop' => [
                    (int) $showDimensions['width'],
                    (int) $showDimensions['height'],
                    (int) $showDimensions['left'],
                    (int) $showDimensions['top'],
                ],
                'width' => [config('media-library.image_dimensions.recipe.conversions.show.width')],
            ];
        }

        $manipulations = [
            'card' => $manipulationsCard ?? [],
            'show' => $manipulationsShow ?? [],
        ];

        // New media
        if ($request->hasFile('media')) {
            $recipe->addMediaFromRequest('media')
                ->withManipulations($manipulations)
                ->toMediaCollection('recipe_image');
        }

        // Existing media
        $media = $recipe->getFirstMedia('recipe_image');
        if ($media && $media->manipulations !== $manipulations) {
            $media->manipulations = $manipulations;
            $media->save();
        }
    }

    // @see https://developers.google.com/search/docs/appearance/structured-data/recipe
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

        if ($recipe->preparation_minutes && $recipe->cooking_minutes) {
            JsonLd::addValues([
                'prepTime' => $this->minutesToISODuration($recipe->preparation_minutes),
                'cookTime' => $this->minutesToISODuration($recipe->cooking_minutes),
            ]);
        }

        if ($recipe->preparation_minutes || $recipe->cooking_minutes) {
            JsonLd::addValue('totalTime', $this->minutesToISODuration(($recipe->preparation_minutes ?? 0) + ($recipe->cooking_minutes ?? 0)));
        }

        if ($recipe->tags->count() > 0) {
            $keywords = $recipe->tags->map(fn ($tag) => $tag->getTranslation('name', $translation->locale))->filter()->toArray();
            if (! empty($keywords)) {
                JsonLd::addValue('keywords', implode(',', $keywords));
            }
        }
    }

    /*
     * Transform minutes into an ISO 8601 duration string.
     * @see https://en.wikipedia.org/wiki/ISO_8601#Durations
     */
    private function minutesToISODuration($minutes): ?string
    {
        $isoHours = (int) $minutes > 59 ? floor($minutes / 60).'H' : '';
        $isoMinutes = (int) $minutes % 60 ? ($minutes % 60).'M' : '';

        if (empty($isoMinutes) && empty($isoHours)) {
            return null;
        }

        return 'PT'.$isoHours.$isoMinutes;
    }

    private function formatOpenGraphLocale(string $locale): string
    {
        $localeMap = [
            'nl' => 'nl_NL',
            'en' => 'en_US',
            'de' => 'de_DE',
            'fr' => 'fr_FR',
            'es' => 'es_ES',
            'it' => 'it_IT',
            'pt' => 'pt_PT',
            'ru' => 'ru_RU',
            'ja' => 'ja_JP',
            'zh' => 'zh_CN',
        ];

        return $localeMap[$locale] ?? $locale.'_'.strtoupper($locale);
    }
}
