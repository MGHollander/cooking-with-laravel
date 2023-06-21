<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $search  = $request->get('search') ?? '';
        $recipes = Search::add(Recipe::class, ['title', 'ingredients', 'instructions'])
            ->paginate(15)
            ->beginWithWildcard()
            ->search($search)
            ->withQueryString()
            ->through(fn($recipe) => [
                'id'    => $recipe->id,
                'title' => $recipe->title,
                'slug'  => $recipe->slug,
                'image' => $recipe->image ? Storage::url($recipe->image) : null,
            ]);

        return Inertia::render('Search/Index', [
            'recipes' => $recipes,
            'search'  => $search,
        ]);
    }

}
