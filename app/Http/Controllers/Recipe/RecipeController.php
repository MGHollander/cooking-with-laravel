<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Resources\Recipe\IngredientsResource;
use App\Http\Resources\StructuredData\Recipe\IngredientsResource as StructuredDataIngredientsResource;
use App\Http\Resources\StructuredData\Recipe\InstructionsResource;
use App\Http\Traits\FillableAttributes;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class RecipeController extends Controller
{
    use FillableAttributes;

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Recipes/Index', [
            'recipes' => Recipe::query()
                ->paginate(17)
                ->through(fn($recipe) => [
                    'id'    => $recipe->id,
                    'title' => $recipe->title,
                    'slug'  => $recipe->slug,
                    'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
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
        return Inertia::render('Recipes/Form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RecipeRequest $request
     * @return RedirectResponse
     */
    public function store(RecipeRequest $request)
    {
        $attributes            = $request->validated();
        $attributes['user_id'] = $request->user()->id;
        // TODO Make a mutator for this. Also for the update method.
        $attributes['tags'] = !empty($attributes['tags']) ? array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags'])))) : [];

        $recipe = (new Recipe)->create($this->fillableAttributes(new Recipe, $attributes));

        $this->saveMedia($request, $recipe);

        return redirect()->route('recipes.show', $recipe->slug)->with('success', "Het recept “<i>{$recipe->title}</i>” is succesvol toegevoegd!");
    }

    /**
     * Display the specified resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $slug
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response|\Inertia\Response
     */
    public function show(Request $request, string $slug)
    {
        $recipe = Recipe::findBySlug($slug);

        if (!$recipe) {
            return $this->notFound($request, $slug);
        }

        return Inertia::render('Recipes/Show', [
            'recipe' => [
                'id'                  => $recipe->id,
                'title'               => $recipe->title,
                'slug'                => $recipe->slug,
                'image'               => $recipe->getFirstMediaUrl('recipe_image', 'show'),
                'summary'             => $recipe->summary,
                'tags'                => $recipe->tags->pluck('name'),
                'servings'            => $recipe->servings,
                'preparation_minutes' => $recipe->preparation_minutes,
                'cooking_minutes'     => $recipe->cooking_minutes,
                'difficulty'          => Str::ucfirst(__('recipes.' . $recipe->difficulty)),
                'ingredients'         => new IngredientsResource($recipe->ingredients),
                'instructions'        => $recipe->instructions,
                'source_label'        => $recipe->source_label,
                'source_link'         => $recipe->source_link,
                'created_at'          => $recipe->created_at,
                'structured_data'     => [
                    'description'  => strip_tags($recipe->summary),
                    'ingredients'  => new StructuredDataIngredientsResource($recipe->ingredients),
                    'instructions' => new InstructionsResource($recipe->instructions),
                    'keywords'     => implode(',', $recipe->tags->pluck('name')->toArray()),
                ],
            ],
        ])->withViewData([
            'open_graph' => [
                'title' => $recipe->title,
                'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
                'url'   => URL::current(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Recipe $recipe
     * @return \Inertia\Response
     */
    public function edit(Recipe $recipe)
    {
        return Inertia::render('Recipes/Form', [
            'recipe' => [
                'id'                  => $recipe->id,
                'title'               => $recipe->title,
                'slug'                => $recipe->slug,
                'media'               => $recipe->getFirstMedia('recipe_image'),
                'summary'             => $recipe->summary,
                'tags'                => $recipe->tags->pluck('name')->implode(', '),
                'servings'            => $recipe->servings,
                'preparation_minutes' => $recipe->preparation_minutes,
                'cooking_minutes'     => $recipe->cooking_minutes,
                'difficulty'          => $recipe->difficulty,
                'ingredients'         => $recipe->ingredients,
                'instructions'        => $recipe->instructions,
                'source_label'        => $recipe->source_label,
                'source_link'         => $recipe->source_link,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RecipeRequest $request
     * @param Recipe        $recipe
     * @return RedirectResponse
     */
    public function update(RecipeRequest $request, Recipe $recipe)
    {
        $attributes         = $request->validated();
        $attributes['tags'] = !empty($attributes['tags']) ? array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags'])))) : [];

        $recipe->update($this->fillableAttributes($recipe, $attributes));

        if ($request->get('destroy_media', false)) {
            $recipe->clearMediaCollection('recipe_image');
        }

        $this->saveMedia($request, $recipe);

        return redirect()->route('recipes.show', $recipe->slug)->with('success', "Het recept “<i>{$recipe->title}</i>” is succesvol gewijzigd!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Recipe $recipe
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return redirect()->route('home')->with('success', "Het recept “<i>{$recipe->title}</i>” is succesvol verwijderd!");
    }

    private function notFound(Request $request, $slug): \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $q       = Str::replace('-', ' ', $slug);
        $recipes = Search::add(Recipe::class, ['title', 'ingredients', 'instructions', 'tags.name'])
            ->paginate(12)
            ->beginWithWildcard()
            ->search($q)
            ->withQueryString()
            ->through(fn($recipe) => [
                'id'    => $recipe->id,
                'title' => $recipe->title,
                'slug'  => $recipe->slug,
                'image' => $recipe->getFirstMedia('recipe_image', 'card'),
            ]);

        return Inertia::render('Recipes/NotFound', [
            'q'       => implode(', ', explode(' ', $q)),
            'recipes' => $recipes,
        ])
            ->toResponse($request)
            ->setStatusCode(404);
    }

    private function saveMedia(Request $request, Recipe $recipe): void
    {
        $mediaDimensions = $request->get('media_dimensions', []);

        if (!empty($mediaDimensions['card'])) {
            $cardDimensions    = $mediaDimensions['card'];
            $manipulationsCard = ['manualCrop' => "${cardDimensions['width']},${cardDimensions['height']},${cardDimensions['left']},${cardDimensions['top']}"];
        }

        if (!empty($mediaDimensions['show'])) {
            $showDimensions    = $mediaDimensions['show'];
            $manipulationsShow = ['manualCrop' => "${showDimensions['width']},${showDimensions['height']},${showDimensions['left']},${showDimensions['top']}"];
        }

        if ($request->hasFile('media')) {
            $recipe->addMediaFromRequest('media')
                ->withManipulations([
                    'card' => $manipulationsCard ?? [],
                    'show' => $manipulationsShow ?? [],
                ])
                ->toMediaCollection('recipe_image');
        }

        if ($media = $recipe->getFirstMedia('recipe_image')) {
            $media->manipulations = [
                'card' => $manipulationsCard ?? [],
                'show' => $manipulationsShow ?? [],
            ];
            $media->save();
        }
    }
}
