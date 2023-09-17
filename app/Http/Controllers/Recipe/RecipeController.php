<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Resources\Recipe\IngredientsResource;
use App\Http\Resources\StructuredData\Recipe\IngredientsResource as StructuredDataIngredientsResource;
use App\Http\Resources\StructuredData\Recipe\InstructionsResource;
use App\Http\Traits\UploadImageTrait;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class RecipeController extends Controller
{
    use UploadImageTrait;

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
                    'image' => $recipe->image ? Storage::disk('public')->url($recipe->image) : null,
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

        if ($image = $this->saveImage($request)) {
            $attributes['image'] = $image;
        }

        $attributes['tags'] = !empty($attributes['tags']) ? array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags'])))) : [];


        $recipe = Recipe::create($attributes);

        return redirect()->route('recipes.show', $recipe)->with('success', "Het recept “<i>{$recipe->title}</i>” is succesvol toegevoegd!");
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
                'image'               => $recipe->image ? Storage::disk('public')->url($recipe->image) : null,
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
                'image' => $recipe->image ? Storage::disk('public')->url($recipe->image) : null,
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
        if ($recipe->image) {
            $recipe->image = Storage::disk('public')->url($recipe->image);
        }

        return Inertia::render('Recipes/Form', [
            'recipe' => [
                'id'                  => $recipe->id,
                'title'               => $recipe->title,
                'slug'                => $recipe->slug,
                'image'               => $recipe->image,
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
        $attributes   = $request->validated();
        $destroyImage = $request->get('destroy_image', false);

        // If the image field is empty, remove it from the attributes array, because there is no image to update or delete.
        if (empty($attributes['image'])) {
            unset($attributes['image']);
        }

        if ($image = $this->saveImage($request)) {
            $attributes['image'] = $image;

            // Delete the old image if there is one.
            if ($recipe->image) {
                $this->destroyImage($recipe);
            }
        }

        // If the user wants to remove the image, set the image field to null and delete the image.
        if ($destroyImage) {
            $attributes['image'] = null;
            $this->destroyImage($recipe);
        }

        $attributes['tags'] = !empty($attributes['tags']) ? array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags'])))) : [];

        $recipe->update($attributes);

        return redirect()->route('recipes.show', $recipe)->with('success', "Het recept “<i>{$recipe->title}</i>” is succesvol gewijzigd!");
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
                'image' => $recipe->image ? Storage::url($recipe->image) : null,
            ]);

        return Inertia::render('Recipes/NotFound', [
            'q'       => implode(', ', explode(' ', $q)),
            'recipes' => $recipes,
        ])
            ->toResponse($request)
            ->setStatusCode(404);
    }
}
