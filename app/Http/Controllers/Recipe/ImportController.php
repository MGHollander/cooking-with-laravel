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
use App\Services\RecipeParsing\Data\ParsedRecipeData;
use App\Services\RecipeParsing\Services\RecipeParsingService;
use App\Support\FileHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ImportController extends Controller
{
    use FillableAttributes;

    public function __construct(
        private readonly RecipeParsingService $recipeParsingService,
        private readonly ImportLogService $importLogService
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('Import/Index', [
            'url' => $request->session()->get('import_url'),
            'parser' => $request->session()->get('parser'),
            'forceImport' => $request->session()->get('force_import'),
            'openAI' => $this->recipeParsingService->isParserAvailable('open-ai'),
            'firecrawl' => $this->recipeParsingService->isParserAvailable('firecrawl'),
        ]);
    }

    public function create(ImportRequest $request)
    {
        $url = $request->get('url');
        $cleanUrl = FileHelper::cleanUrl($url);
        $parser = $request->get('parser', 'structured-data');
        $forceImport = filter_var($request->get('force_import', false), FILTER_VALIDATE_BOOLEAN);
        $user = $request->user();

        // Check if current user already imported this URL
        $userImport = $this->importLogService->getUserImportForUrl($user, $cleanUrl);

        if (! $forceImport && $userImport && $userImport->recipe) {
            $recipeUrl = route('recipes.show', $userImport->recipe->slug);

            return back()
                ->with('warning', "Je hebt dit recept al geïmporteerd: <a href=\"{$recipeUrl}\">{$userImport->recipe->title}</a>")
                ->with('import_url', $url);
        }

        // Render form with loading state
        return Inertia::render('Import/Form', [
            'url' => $cleanUrl,
            'parser' => $parser,
            'force_import' => $forceImport,
            'config' => [
                'image_dimensions' => config('media-library.image_dimensions.recipe'),
            ],
        ]);
    }

    private function parseAndPrepareRecipeData(string $cleanUrl, string $parser, bool $forceImport, $user): array
    {
        // Check existing import
        $existingImport = $this->importLogService->getLastNonLocalImportForUrl($cleanUrl);

        if (! $forceImport && $existingImport && $existingImport->parsed_data) {
            Log::info('Import recipe from import logs', [
                'url' => $cleanUrl,
                'import_logs_id' => $existingImport->id,
                'user_id' => $user->id,
            ]);

            try {
                $parsedData = ParsedRecipeData::fromArray($existingImport->parsed_data);
                if (! $parsedData->isValid()) {
                    throw new \InvalidArgumentException('Invalid parsed data from existing import');
                }

                $importLog = $this->importLogService->createLocalImportLog(
                    $cleanUrl,
                    $user,
                    $existingImport->parsed_data
                );

                $recipe = new ImportResource($parsedData->toArray());

                return [
                    'recipe' => $recipe,
                    'import_log_id' => $importLog->id,
                    'config' => [
                        'image_dimensions' => config('media-library.image_dimensions.recipe'),
                    ],
                ];
            } catch (\Exception $e) {
                Log::warning('Failed to reuse existing import data', [
                    'url' => $cleanUrl,
                    'existing_import_id' => $existingImport->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Proceed with normal API parsing
        try {
            if ($forceImport) {
                Log::info('Force recipe import', [
                    'url' => $cleanUrl,
                    'user_id' => $user->id,
                ]);
            }

            if ($parser === 'auto') {
                $parsingResult = $this->recipeParsingService->parseWithFallback($cleanUrl);
                if ($parsingResult) {
                    $parser = $parsingResult['parser'];
                    $parsedResult = $parsingResult['result'];
                } else {
                    $parsedResult = null;
                }
            } else {
                $parsedResult = $this->recipeParsingService->parseWithParser($cleanUrl, $parser);
            }

            if (! $parsedResult || ! $parsedResult->isValid()) {
                throw new \Exception('Helaas, het is niet gelukt om een recept te vinden op deze pagina. Je kan een andere methode proberen. Als dat niet werkt, dan moet je het recept handmatig invoeren.');
            }

            $importLog = $this->importLogService->logSuccessfulImport(
                $cleanUrl,
                $parser,
                $user,
                $parsedResult
            );

            $recipe = new ImportResource($parsedResult->recipe->toArray());

            return [
                'recipe' => $recipe,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to import recipe', [
                'url' => $cleanUrl,
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function importRecipe(Request $request)
    {
        $url = $request->get('url');
        $cleanUrl = FileHelper::cleanUrl($url);
        $parser = $request->get('parser', 'structured-data');
        $forceImport = filter_var($request->get('force_import', false), FILTER_VALIDATE_BOOLEAN);
        $user = $request->user();

        try {
            $data = $this->parseAndPrepareRecipeData($cleanUrl, $parser, $forceImport, $user);

            return response()->json($data);
        } catch (\Exception $e) {
            $request->session()->flash('warning', $e->getMessage());
            $request->session()->flash('import_url', $url);
            $request->session()->flash('parser', $parser);
            $request->session()->flash('force_import', $forceImport);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Proxy external images to avoid CORS issues
     */
    public function proxyImage(Request $request)
    {
        $url = $request->get('url');

        if (! $url) {
            return response()->json(['error' => 'URL parameter is required'], 400);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::get($url);

            if ($response->successful()) {
                return response($response->body())
                    ->header('Content-Type', $response->header('Content-Type'))
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET')
                    ->header('Access-Control-Allow-Headers', '*')
                    ->header('Cache-Control', 'public, max-age=3600');
            }

            return response()->json(['error' => 'Failed to fetch image'], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch image: ' . $e->getMessage()], 500);
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
            $mediaDimensions = $request->get('media_dimensions', []);
            $manipulations = $this->buildManipulations($mediaDimensions);

            try {
                $recipe->addMediaFromUrl($externalImage)
                    ->withManipulations($manipulations)
                    ->toMediaCollection('recipe_image');
            } catch (\Exception $e) {
                Log::error('Failed to save the recipe image from url', [
                    'recipe_id' => $recipe->id,
                    'error' => $e->getMessage(),
                ]);
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
                Log::warning('Failed to update import log with recipe', [
                    'recipe_id' => $recipe->id,
                    'import_log_id' => $importLogId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($request->get('return_to_import_page')) {
            return redirect()->route('import.index')->with('success', 'Het recept "<a href="' . route('recipes.show', $recipe->slug) . '"><i>' . $recipe->title . '</i></a>" is succesvol geïmporteerd!');
        }

        return Inertia::location(route('recipes.show', $recipe->id));
    }

    // TODO dry this code. Is pretty much the same as in app/Http/Controllers/Recipe/RecipeController.php@saveMedia
    private function buildManipulations(array $mediaDimensions): array
    {
        $manipulations = [];

        if (! empty($mediaDimensions['card'])) {
            $cardDimensions = $mediaDimensions['card'];
            $manipulations['card'] = [
                'manualCrop' => [
                    (int) $cardDimensions['width'],
                    (int) $cardDimensions['height'],
                    (int) $cardDimensions['left'],
                    (int) $cardDimensions['top'],
                ],
                'width' => [config('media-library.image_dimensions.recipe.conversions.card.width')],
            ];
        }

        if (! empty($mediaDimensions['show'])) {
            $showDimensions = $mediaDimensions['show'];
            $manipulations['show'] = [
                'manualCrop' => [
                    (int) $showDimensions['width'],
                    (int) $showDimensions['height'],
                    (int) $showDimensions['left'],
                    (int) $showDimensions['top'],
                ],
                'width' => [config('media-library.image_dimensions.recipe.conversions.show.width')],
            ];
        }

        return $manipulations;
    }
}
