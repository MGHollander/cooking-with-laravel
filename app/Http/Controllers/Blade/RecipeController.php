<?php

namespace App\Http\Controllers\Blade;

use App\Http\Controllers\Controller;
use App\Http\Resources\Recipe\IngredientsResource;
use App\Http\Resources\StructuredData\Recipe\IngredientsResource as StructuredDataIngredientsResource;
use App\Http\Resources\StructuredData\Recipe\InstructionsResource;
use App\Http\Traits\FillableAttributes;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class RecipeController extends Controller
{
  use FillableAttributes;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\View\View
   */
  public function index(Request $request): \Illuminate\View\View
  {
    // TODO use a resource instead of a query
    // TODO remove query string from url when empty search field is send.
    return view('blade.recipe.index', [
      'recipes' => Search::add(Recipe::class, ['title', 'ingredients', 'instructions', 'tags.name'])
        ->paginate(12)
        ->beginWithWildcard()
        ->search($request->get("search"))
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
   * @return \Illuminate\View\View
   */
  public function show(Request $request, string $slug): \Illuminate\View\View
  {
    $recipe = Recipe::findBySlug($slug);
    // TODO Make this a resource so that it returns an object in blade.
    return view('blade.recipe.show', [
      'recipe'     => [
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
        // TODO I think this is not the way to go, but for the experiment it's fine.
        'ingredients'         => (new IngredientsResource(""))->transformIngredients($recipe->ingredients),
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
      // TODO Add open graph to layout.
      'open_graph' => [
        'title' => $recipe->title,
        'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
        'url'   => URL::current(),
      ],
    ]);
  }
}
