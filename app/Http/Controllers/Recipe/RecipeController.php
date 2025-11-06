<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Resources\Recipe\IngredientsResource;
use App\Http\Resources\StructuredData\Recipe\IngredientsResource as StructuredDataIngredientsResource;
use App\Http\Resources\StructuredData\Recipe\InstructionsResource as StructuredDataInstructionsResource;
use App\Http\Traits\FillableAttributes;
use App\Models\Recipe;
use Artesaos\SEOTools\Facades\JsonLd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
                ->whereHas('author')
                ->orderBy('id', 'desc')
                ->paginate(12)
                ->through(fn ($recipe) => [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'slug' => $recipe->slug,
                    'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                    'no_index' => $recipe->no_index,
                ]),
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
        // TODO Make a mutator for this. Also for the update method.
        $attributes['tags'] = ! empty($attributes['tags']) ? array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags'])))) : [];

        $recipe = (new Recipe)->create($this->fillableAttributes(new Recipe, $attributes));

        $this->saveMedia($request, $recipe);

        Session::flash('success', 'Het recept is succesvol toegevoegd! 🧑‍🍳');

        return Inertia::location(route('recipes.edit', $recipe->id));
    }

    /**
     * Display the specified resource.
     */
    // TODO Are these return types correct? Should the doc blocks exisit at all or is it overkill with typing?
    public function show(Request $request, string $slug): JsonResponse|View|Response
    {
        $recipe = Recipe::where('slug', $slug)->whereHas('author')->first();

        if (! $recipe) {
            return $this->notFound($slug);
        }

        $this->setJsonLdData($recipe);

        return view('kocina.recipes.show', [
            'recipe' => [
                'id' => $recipe->id,
                'author' => $recipe->author->name,
                'user_id' => $recipe->user_id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
                'summary' => strip_tags($recipe->summary, '<strong><em><u>'),
                'tags' => $recipe->tags->pluck('name'),
                'servings' => $recipe->servings,
                'preparation_minutes' => $recipe->preparation_minutes,
                'cooking_minutes' => $recipe->cooking_minutes,
                'difficulty' => Str::ucfirst(__('recipes.'.$recipe->difficulty)),
                // TODO I think this is not the way to go, but for the experiment it's fine.
                'ingredients' => (new IngredientsResource(''))->transformIngredients($recipe->ingredients),
                'instructions' => strip_tags($recipe->instructions, '<strong><em><u><h3><ol><ul><li>'),
                'source_label' => $recipe->source_label,
                'source_link' => $recipe->source_link,
                'created_at' => $recipe->created_at,
                'no_index' => $recipe->no_index,
            ],
            // TODO Replace for SEO Tools
            'open_graph' => [
                'title' => $recipe->title,
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
        return Inertia::render('Recipes/Form', [
            'recipe' => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'media' => $recipe->getFirstMedia('recipe_image'),
                'summary' => strip_tags($recipe->summary, '<strong><em><u>'),
                'tags' => $recipe->tags->pluck('name')->implode(', '),
                'servings' => $recipe->servings,
                'preparation_minutes' => $recipe->preparation_minutes,
                'cooking_minutes' => $recipe->cooking_minutes,
                'difficulty' => $recipe->difficulty,
                'ingredients' => $recipe->ingredients,
                'instructions' => strip_tags($recipe->instructions, '<strong><em><u><h3><ol><ul><li>'),
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
        $attributes['tags'] = ! empty($attributes['tags']) ? array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags'])))) : [];

        $recipe->update($this->fillableAttributes($recipe, $attributes));

        if ($request->get('destroy_media', false)) {
            $recipe->clearMediaCollection('recipe_image');
        }

        $this->saveMedia($request, $recipe);

        Session::flash('success', 'Het recept is succesvol gewijzigd!  🧑‍🍳');

        return Inertia::location(route('recipes.show', $recipe->slug));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Recipe $recipe)
    {
        $userId = auth()->id();
        $recipeId = $recipe->id;
        
        $recipe->deletePreservingMedia();
        
        Log::info("Recipe {$recipeId} deleted by user {$userId}");

        Session::flash('success', "Het recept “<i>{$recipe->title}</i>” is succesvol verwijderd! 🧑‍🍳");

        return Inertia::location(route('home'));
    }

    private function notFound($slug): \Illuminate\Http\Response
    {
        $searchKey = Str::replace('-', ' ', $slug);
        $recipes = Search::add(Recipe::class, ['title', 'ingredients', 'instructions', 'tags.name'])
            ->paginate(12)
            ->beginWithWildcard()
            ->search($searchKey)
            ->withQueryString()
            ->through(fn ($recipe) => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                'no_index' => $recipe->no_index,
            ]);

        return response()->view(
            'kocina.recipes.not-found',
            [
                'recipes' => $recipes,
                'searchKey' => implode(', ', explode(' ', $searchKey)),
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
    private function setJsonLdData(Recipe $recipe): void
    {
        JsonLd::setType('Recipe');
        JsonLd::setTitle($recipe->title);

        if ($recipe->summary) {
            JsonLd::setDescription(strip_tags($recipe->summary));
        }

        JsonLd::addValues([
            'datePublished' => $recipe->created_at,
            'recipeYield' => $recipe->servings,
            'recipeIngredient' => new StructuredDataIngredientsResource($recipe->ingredients),
            'recipeInstructions' => new StructuredDataInstructionsResource($recipe->instructions),
        ]);

        $image = $recipe->getFirstMediaUrl('recipe_image', 'show');
        if ($image) {
            JsonLd::addImage($image);
        }

        // prepTime and cookTime should be used together according to the Google specs.
        // Therefore, only add if both of them are available.
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
