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
        $locale = app()->getLocale();
        
        $recipes = Search::new()
            ->add(
                Recipe::with('author', 'media', 'tags', 'translations')
                    ->whereHas('author'),
                ['translations.title', 'translations.ingredients', 'translations.instructions', 'tags.name']
            )
            ->paginate(12)
            ->beginWithWildcard()
            ->search(strtolower($searchKey))
            ->withQueryString()
            ->through(function ($recipe) use ($locale) {
                $translation = $recipe->translate($locale) 
                    ?? $recipe->translate(config('app.fallback_locale'));
                
                return [
                    'id' => $recipe->id,
                    'title' => $translation?->title ?? 'Untitled',
                    'slug' => $translation?->slug ?? '',
                    'locale' => $translation?->locale ?? config('app.fallback_locale'),
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
