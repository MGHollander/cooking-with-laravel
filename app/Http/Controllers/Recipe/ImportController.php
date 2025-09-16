<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportRequest;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Resources\Recipe\ImportResource;
use App\Http\Traits\FillableAttributes;
use App\Models\Recipe;
use App\Services\RecipeParsing\Services\RecipeParsingService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ImportController extends Controller
{
    use FillableAttributes;

    public function __construct(
        private readonly RecipeParsingService $recipeParsingService
    ) {
    }

    public function index()
    {
        return Inertia::render('Import/Index', [
            'openAI' => $this->recipeParsingService->isParserAvailable('open-ai'),
            'firecrawl' => $this->recipeParsingService->isParserAvailable('firecrawl'),
        ]);
    }

    public function create(ImportRequest $request)
    {
        $url = $request->get('url');
        $parser = $request->get('parser', 'structured-data');

        try {
            $parsedRecipe = $this->recipeParsingService->parseWithParser($url, $parser);
            
            if (!$parsedRecipe) {
                return back()->with('warning', 'Helaas, het is niet gelukt om een recept te vinden op deze pagina. Je kan een andere methode proberen. Als dat niet werkt, dan moet je het recept handmatig invoeren.');
            }

            $recipe = new ImportResource($parsedRecipe->toArray());

            return Inertia::render('Import/Form', [
                'url' => $url,
                'recipe' => $recipe,
            ]);

        } catch (\Exception $e) {
            return back()->with('warning', 'Er is een fout opgetreden bij het ophalen van het recept: ' . $e->getMessage() . '. Probeer een andere methode of voer het recept handmatig in.');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(RecipeRequest $request)
    {
        $attributes = $request->validated();
        $attributes['user_id'] = $request->user()->id;
        // TODO Make a mutator for this.
        // Convert tags to lowercase and trim whitespace.
        $attributes['tags'] = ! empty($attributes['tags']) ? array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags'])))) : [];

        $recipe = (new Recipe)->create($this->fillableAttributes(new Recipe, $attributes));

        if ($externalImage = $request->get('external_image')) {
            try {
                $recipe->addMediaFromUrl($externalImage)
                    ->toMediaCollection('recipe_image');
            } catch (\Exception $e) {
                // TODO handle exception
            }
        }

        if ($request->get('return_to_import_page')) {
            return redirect()->route('import.index')->with('success', 'Het recept “<a href="'.route('recipes.show', $recipe->slug).'"><i>'.$recipe->title.'</i></a>” is succesvol geïmporteerd!');
        }

        return redirect()->route('recipes.edit', $recipe->id)->with('success', "Het recept “<i>{$recipe->title}</i>” is succesvol geïmporteerd!");
    }

}
