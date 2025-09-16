<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportRequest;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Resources\Recipe\ImportResource;
use App\Http\Traits\FillableAttributes;
use App\Models\Recipe;
use App\Support\FirecrawlRecipeParser;
use App\Support\OpenAIRecipeParser;
use App\Support\StructuredDataRecipeParser;
use Brick\StructuredData\HTMLReader;
use Brick\StructuredData\Reader\JsonLdReader;
use Brick\StructuredData\Reader\MicrodataReader;
use Brick\StructuredData\Reader\RdfaLiteReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class ImportController extends Controller
{
    use FillableAttributes;

    public function index()
    {
        return Inertia::render('Import/Index', [
            'openAI' => ! empty(config('services.open_ai.api_key')),
            'firecrawl' => ! empty(config('services.firecrawl.api_key')),
        ]);
    }

    public function create(ImportRequest $request)
    {
        $url = $request->get('url');
        $parser = $request->get('parser', 'structured-data');

        match ($parser) {
            'structured-data' => $recipe = $this->parseStructuredData($url),
            'open-ai' => $recipe = new ImportResource(OpenAIRecipeParser::read($url)),
            'firecrawl' => $recipe = new ImportResource(FirecrawlRecipeParser::read($url)),
        };

        if (! $recipe) {
            return back()->with('warning', 'Helaas, het is niet gelukt om een recept te vinden op deze pagina. Je kan een andere methode proberen. Als dat niet werkt, dan moet je het recept handmatig invoeren.');
        }

        return Inertia::render('Import/Form', [
            'url' => $url,
            'recipe' => $recipe ?? null,
        ]);
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

    private function parseStructuredData($url)
    {
        try {
            $response = Http::throw()->get($url);
        } catch (\Exception $e) {
            return null;
        }

        // The XML HTML readers don't handle UTF-8 for you
        $html = mb_convert_encoding($response->body(), 'HTML-ENTITIES', 'UTF-8');

        $readers = [
            'JsonLdReader' => new HTMLReader(new JsonLdReader),
            'MicrodataReader' => new HTMLReader(new MicrodataReader),
            'RdfaLiteReader' => new HTMLReader(new RdfaLiteReader),
        ];

        foreach ($readers as $reader) {
            $items = $reader->read($html, $url);

            if ($recipe = StructuredDataRecipeParser::fromItems($items, $url)) {
                break;
            }
        }

        return $recipe ?? null;
    }
}
