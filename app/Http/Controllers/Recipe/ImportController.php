<?php

namespace App\Http\Controllers\Recipe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportRequest;
use App\Http\Requests\Recipe\RecipeRequest;
use App\Http\Resources\Recipe\ImportResource;
use App\Http\Traits\FillableAttributes;
use App\Models\ImportLog;
use App\Models\Recipe;
use App\Services\ImportLogService;
use App\Services\RecipeParsing\Services\RecipeParsingService;
use App\Support\FileHelper;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class ImportController extends Controller
{
    use FillableAttributes;

    public function __construct(
        private readonly RecipeParsingService $recipeParsingService,
        private readonly ImportLogService $importLogService
    ) {}

    public function index()
    {
        return Inertia::render('Import/Index', [
            'openAI' => $this->recipeParsingService->isParserAvailable('open-ai'),
            'firecrawl' => $this->recipeParsingService->isParserAvailable('firecrawl'),
        ]);
    }

    public function create(ImportRequest $request)
    {
        $url = FileHelper::cleanUrl($request->get('url'));
        $parser = $request->get('parser', 'structured-data');

        try {
            $parsedRecipe = $this->recipeParsingService->parseWithParser($url, $parser);

            if (! $parsedRecipe) {
                return back()->with('warning', 'Helaas, het is niet gelukt om een recept te vinden op deze pagina. Je kan een andere methode proberen. Als dat niet werkt, dan moet je het recept handmatig invoeren.');
            }

            // Log successful import immediately after parsing succeeds
            $importLog = $this->importLogService->logSuccessfulImport(
                $url,
                $parser,
                $request->user(),
                $parsedRecipe
            );

            $recipe = new ImportResource($parsedRecipe->toArray());

            return Inertia::render('Import/Form', [
                'url' => $url,
                'recipe' => $recipe,
                'import_log_id' => $importLog->id, // Pass the import log ID to connect it later
            ]);

        } catch (\Exception $e) {
            return back()->with('warning', 'Er is een fout opgetreden bij het ophalen van het recept. Probeer een andere methode of voer het recept handmatig in.');
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

        // Update import log with created recipe if this was imported from a URL
        if ($importLogId = $request->get('import_log_id')) {
            try {
                $importLog = ImportLog::find($importLogId);
                if ($importLog) {
                    $this->importLogService->updateImportLogWithRecipe($importLog, $recipe);
                }
            } catch (\Exception $e) {
                // Don't fail the recipe creation if import log update fails
                \Illuminate\Support\Facades\Log::warning('Failed to update import log with recipe', [
                    'recipe_id' => $recipe->id,
                    'import_log_id' => $importLogId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($request->get('return_to_import_page')) {
            return redirect()->route('import.index')->with('success', 'Het recept "<a href="'.route('recipes.show', $recipe->slug).'"><i>'.$recipe->title.'</i></a>" is succesvol geïmporteerd!');
        }

        return redirect()->route('recipes.edit', $recipe->id)->with('success', 'Het recept "'.$recipe->title.'" is succesvol geïmporteerd!');
    }
}
