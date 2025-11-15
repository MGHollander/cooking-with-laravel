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
use Artesaos\SEOTools\Facades\JsonLd;
use Illuminate\Http\JsonResponse;
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
use App\Support\ImageTypeHelper;

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
                        'slug' => $translation->slug,
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
                'summary' => $attributes['summary'],
                'ingredients' => $attributes['ingredients'],
                'instructions' => $attributes['instructions'],
            ]);
            
            if (!empty($attributes['tags'])) {
                $tags = array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags']))));
                $recipe->syncTags($tags);
            }
            
            $this->saveMedia($request, $recipe);
        });
        
        Session::flash('success', 'Het recept is succesvol toegevoegd! 🧑‍🍳');
        
        return Inertia::location(route('recipes.edit', $recipe->id));
    }

    /**
     * Display the specified resource.
     */
    // TODO Are these return types correct? Should the doc blocks exisit at all or is it overkill with typing?
    public function show(Request $request, string $slug): JsonResponse|View|Response
    {
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
            
            $recipe->translations()->updateOrCreate(
                ['locale' => $attributes['locale']],
                [
                    'title' => $attributes['title'],
                    'summary' => $attributes['summary'],
                    'ingredients' => $attributes['ingredients'],
                    'instructions' => $attributes['instructions'],
                ]
            );
            
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
        
        Session::flash('success', 'Het recept is succesvol gewijzigd!  🧑‍🍳');
        
        $slug = $recipe->getSlugForLocale($attributes['locale']);
        return Inertia::location(route('recipes.show', $slug));
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

        Session::flash('success', "Het recept \"<i>{$translation->title}</i>\" is succesvol verwijderd! 🧑‍🍳");

        return Inertia::location(route('home'));
    }

    private function notFound($slug): \Illuminate\Http\Response
    {
        $searchKey = Str::replace('-', ' ', $slug);
        $recipes = Search::add(RecipeTranslation::with('recipe.media'), ['title', 'ingredients', 'instructions'])
            ->add(Recipe::with('translations', 'tags'), ['tags.name'])
            ->paginate(12)
            ->beginWithWildcard()
            ->search($searchKey)
            ->withQueryString()
            ->map(function ($result) {
                if ($result instanceof RecipeTranslation) {
                    $recipe = $result->recipe;
                    return [
                        'id' => $recipe->id,
                        'title' => $result->title,
                        'slug' => $result->slug,
                        'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                        'no_index' => $recipe->no_index,
                    ];
                }
                
                $translation = $result->primaryTranslation();
                return [
                    'id' => $result->id,
                    'title' => $translation->title,
                    'slug' => $translation->slug,
                    'image' => $result->getFirstMediaUrl('recipe_image', 'card'),
                    'no_index' => $result->no_index,
                ];
            });

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
            JsonLd::addValue('keywords', implode(',', $recipe->tags->pluck('name')->toArray()));
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
}
