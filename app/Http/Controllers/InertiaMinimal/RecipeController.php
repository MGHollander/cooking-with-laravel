<?php

namespace App\Http\Controllers\InertiaMinimal;

use App\Http\Controllers\Controller;
use App\Http\Resources\Recipe\IngredientsResource;
use App\Http\Resources\StructuredData\Recipe\IngredientsResource as StructuredDataIngredientsResource;
use App\Http\Resources\StructuredData\Recipe\InstructionsResource;
use App\Http\Traits\FillableAttributes;
use App\Models\Recipe;
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
  public function index(Request $request)
  {
    // TODO use a resource instead of a query
    return Inertia::render('Recipes/Index', [
      'recipes' => Search::add(Recipe::class, ['title', 'ingredients', 'instructions', 'tags.name'])
        ->paginate(12)
        ->beginWithWildcard()
        ->search(strtolower($request->get("search")))
        ->withQueryString()
        ->through(fn($recipe) => [
          'id'    => $recipe->id,
          'title' => $recipe->title,
          'slug'  => $recipe->slug,
          'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
        ]),
      // TODO Add open graph tags.
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param \Illuminate\Http\Request $request
   * @param string                   $slug
   * @return \Inertia\Response
   */
  public function show(Request $request, string $slug)
  {
    $recipe = Recipe::findBySlug($slug);

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
}