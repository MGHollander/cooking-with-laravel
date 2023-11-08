<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class RecipeNotFoundController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Inertia\Response
     */
    public function __invoke(Request $request, array $ids): \Inertia\Response
    {
        $q       = Str::replace('-', ' ', array_shift($ids));
        $recipes = Search::add(Recipe::class, ['title', 'ingredients', 'instructions', 'tags.name'])
            ->paginate(12)
            ->beginWithWildcard()
            ->search($q)
            ->withQueryString()
            ->through(fn($recipe) => [
                'id'    => $recipe->id,
                'title' => $recipe->title,
                'slug'  => $recipe->slug,
                'image' => $recipe->getFirstMediaUrl('recipe_image', 'show'),
            ]);

        // TODO Add 404 header
        // FIXME On this page the user sessions is unknown. This probably has to do with the fact that this
        //  controller is loaded from the missing method when a recipe is nog found (see routes/web.php).
        //  The InertiaHandler has been added as a general middleware to be able to show the default layout,
        return Inertia::render('Recipes/NotFound', [
            'q'       => implode(', ', explode(' ', $q)),
            'recipes' => $recipes,
        ]);
    }
}
