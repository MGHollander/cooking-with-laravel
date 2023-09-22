<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportRequest;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Resources\Recipe\ImportResource;
use App\Http\Traits\UploadImageTrait;
use App\Models\Recipe;
use App\Support\FileHelper;
use App\Support\OpenAIRecipeParser;
use App\Support\StructuredDataRecipeParser;
use Brick\StructuredData\HTMLReader;
use Brick\StructuredData\Reader\JsonLdReader;
use Brick\StructuredData\Reader\MicrodataReader;
use Brick\StructuredData\Reader\RdfaLiteReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ImportController extends Controller
{
    use UploadImageTrait;

    public function index()
    {
        return Inertia::render('Import/Index', [
            'openAI' => !empty(config('services.open_ai.api_key')),
        ]);
    }

    public function create(ImportRequest $request)
    {
        $url    = $request->get('url');
        $parser = $request->get('parser', 'structured-data');

        // TODO: Use proper validation. This is just a quick fix.
        try {
            Http::throw()->get($url);
        } catch (\Exception $e) {
            return back()->with('warning', 'Helaas, de URL die je hebt opgegeven lijkt niet (meer) te bestaan. Probeer een andere URL.');
        }

        match ($parser) {
            'structured-data' => $recipe = $this->parseStructuredData($url),
            'open-ai'         => $recipe = new ImportResource(OpenAIRecipeParser::read($url)),
        };

        if (!$recipe) {
            return back()->with('warning', 'Helaas, het is niet gelukt om een recept te vinden op deze pagina. Heeft deze pagina wel een recept? Je kan een andere methode proberen. Als dat niet werkt, dan moet je het recept handmatig invoeren.');
        }

        return Inertia::render('Import/Form', [
            'url'    => $url,
            'recipe' => $recipe ?? null,
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param RecipeRequest $request
     * @return RedirectResponse
     */
    public function store(RecipeRequest $request)
    {
        $return_to_import_page = $request->get('return_to_import_page');

        $attributes            = $request->validated();
        $attributes['user_id'] = $request->user()->id;

        if ($external_image = $request->get('external_image')) {
            $extension = substr($external_image, strrpos($external_image, '.') + 1);
            try {
                $attributes['image'] = FileHelper::uploadExternalImage(
                    $external_image,
                    Str::slug($attributes['title']) . '-' . time() . '.' . $extension
                );
            } catch (\Exception $e) {
                // @TODO handle exception
            }
        }

        if ($image = $this->saveImage($request)) {
            $attributes['image'] = $image;
        }

        $attributes['tags'] = !empty($attributes['tags']) ? array_filter(array_map('strtolower', array_map('trim', explode(',', $attributes['tags'])))) : [];

        // Always unset external_image, because it's not saved to the database.
        unset($attributes['external_image']);

        $recipe = Recipe::create($attributes);

        if ($return_to_import_page) {
            return redirect()->route('import.index')->with('success', 'Het recept “<a href="' . route('recipes.show', $recipe->slug) . '"><i>' . $recipe->title . '</i></a>” is succesvol geïmporteerd!');
        }
        return redirect()->route('recipes.show', $recipe->slug)->with('success', "Het recept “<i>{$recipe->title}</i>” is succesvol geïmporteerd!");
    }

    private function parseStructuredData($url)
    {
        $response = Http::throw()->get($url);

        // The XML HTML readers don't handle UTF-8 for you
        $html = mb_convert_encoding($response->body(), 'HTML-ENTITIES', 'UTF-8');

        $readers = [
            'JsonLdReader'    => new HTMLReader(new JsonLdReader()),
            'MicrodataReader' => new HTMLReader(new MicrodataReader()),
            'RdfaLiteReader'  => new HTMLReader(new RdfaLiteReader()),
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
