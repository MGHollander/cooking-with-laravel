<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class SearchController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @param Request $request
   * @return \Inertia\Response
   */
  public function index(Request $request): Response
  {
    $q       = $request->get('q') ?? '';
    $recipes = Search::add(Recipe::class, ['title', 'ingredients', 'instructions', 'tags.name'])
      ->paginate(strtolower(15))
      ->beginWithWildcard()
      ->search($q)
      ->withQueryString()
      ->through(fn($recipe) => [
        'id'    => $recipe->id,
        'title' => $recipe->title,
        'slug'  => $recipe->slug,
        'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
      ]);

    return Inertia::render('Search/Index', [
      'recipes' => $recipes,
      'q'       => $q,
    ]);
  }

}
