<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportRequest;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Traits\UploadImageTrait;
use App\Models\Recipe;
use App\Support\FileHelper;
use App\Support\RecipeParser;
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
        return Inertia::render('Import/Index');
    }

    public function create(ImportRequest $request)
    {
        $url = $request->get('url');

        if ($url) {
            $response = Http::throw()->get($url);

            // The XML HTML readers don't handle UTF-8 for you
            $html = mb_convert_encoding($response->body(), 'HTML-ENTITIES', 'UTF-8');

            $parsers = [
                'JsonLdReader'    => new HTMLReader(new JsonLdReader()),
                'MicrodataReader' => new HTMLReader(new MicrodataReader()),
                'RdfaLiteReader'  => new HTMLReader(new RdfaLiteReader()),
            ];

            foreach ($parsers as $parser) {
                $items = $parser->read($html, $url);

                if ($recipe = RecipeParser::fromItems($items, $url)) {
                    break;
                }
            }

            if (!$recipe) {
                return back()->with('warning', 'Helaas, het is niet gelukt om een recept te vinden op deze pagina.');
            }
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
            $extension           = substr($external_image, strrpos($external_image, '.') + 1);
            $attributes['image'] = FileHelper::uploadExternalImage(
                $external_image,
                Str::slug($attributes['title']) . '-' . time() . '.' . $extension
            );
            unset($attributes['external_image']);
        }

        if ($image = $this->saveImage($request)) {
            $attributes['image'] = $image;
        }

        if ($tags = $request->get('tags')) {
            $attributes['tags'] = array_map('trim', explode(',', $tags));
        }
        
        $recipe = Recipe::create($attributes);

        if ($return_to_import_page) {
            return redirect()->route('import.index')->with('success', 'Het recept is succesvol geïmporteerd! <a href="' . route('recipes.show', $recipe) . '">Bekijk het recept</a>.');
        }
        return redirect()->route('recipes.show', $recipe)->with('success', 'Het recept is succesvol geïmporteerd!');
    }
}
