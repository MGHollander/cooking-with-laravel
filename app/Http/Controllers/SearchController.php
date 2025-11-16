<?php

namespace App\Http\Controllers;

use App\Models\RecipeTranslation;
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
        $recipes = Search::new()
            ->add(
                RecipeTranslation::with('recipe.author', 'recipe.media')
                    ->whereHas('recipe', fn ($query) => $query->whereHas('author')),
                ['title', 'ingredients', 'instructions']
            )
            ->paginate(12)
            ->beginWithWildcard()
            ->search(strtolower($searchKey))
            ->withQueryString()
            ->map(function ($translation) {
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
            'searchKey' => $searchKey,
        ]);
    }
}
