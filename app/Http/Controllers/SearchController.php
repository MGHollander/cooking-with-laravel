<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\View\View;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $searchKey = $request->get('q', '');
        $recipes = Search::add(Recipe::with('author')->withActiveAuthor(), ['title', 'ingredients', 'instructions', 'tags.name'])
            ->paginate(12)
            ->beginWithWildcard()
            ->search(strtolower($searchKey))
            ->withQueryString()
            ->through(fn ($recipe) => [
                'id' => $recipe->id,
                'title' => $recipe->title,
                'slug' => $recipe->slug,
                'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
                'no_index' => $recipe->no_index,
            ]);

        return view('kocina.search.index', [
            'recipes' => $recipes,
            'searchKey' => $searchKey,
        ]);
    }
}
