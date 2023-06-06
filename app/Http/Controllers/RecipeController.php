<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Recipes/Index', [
            'recipes' => Recipe::query()
                ->paginate(17)
                ->withQueryString()
                ->through(fn($recipe) => [
                    'id'    => $recipe->id,
                    'title' => $recipe->title,
                    'slug'  => $recipe->slug,
                    'image' => $recipe->image ? Storage::url($recipe->image) : null,
                ]),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Recipes/Form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $attributes            = $this->validateRecipe($request);
        $attributes['user_id'] = $request->user()->id;

        if ($image = $this->saveImage($request)) {
            $attributes['image'] = $image;
        }

        $recipe = Recipe::create($attributes);

        return redirect()->route('recipes.show', $recipe)->with('success', 'Recipe added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param Recipe $recipe
     * @return \Inertia\Response
     */
    public function show(Recipe $recipe)
    {
        if ($recipe->image) {
            $recipe->image = Storage::url($recipe->image);
        }

        if ($recipe->ingredients) {
            $recipe->ingredients = $recipe->transformIngredients($recipe->ingredients);
        }

        return Inertia::render('Recipes/Show', compact('recipe'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Recipe $recipe
     * @return \Inertia\Response
     */
    public function edit(Recipe $recipe)
    {
        if ($recipe->image) {
            $recipe->image = Storage::url($recipe->image);
        }

        return Inertia::render('Recipes/Form', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Recipe  $recipe
     * @return RedirectResponse
     */
    public function update(Request $request, Recipe $recipe)
    {
        $attributes   = $this->validateRecipe($request, $recipe);
        $destroyImage = $request->get('destroy_image', false);

        // If the image field is empty, remove it from the attributes array, because there is no image to update or delete.
        if (empty($attributes['image'])) {
            unset($attributes['image']);
        }

        if ($image = $this->saveImage($request)) {
            $attributes['image'] = $image;

            // Delete the old image if there is one.
            if ($recipe->image) {
                $this->destroyImage($recipe);
            }
        }

        // If the user wants to remove the image, set the image field to null and delete the image.
        if ($destroyImage) {
            $attributes['image'] = null;
            $this->destroyImage($recipe);
        }

        $recipe->update($attributes);

        $redirect = redirect()->route('recipes.show', $recipe);

        if ($destroyImage && !$image) {
            return $redirect->with('warning', 'Recipe updated, but the image is not replaced due to a server error.');
        }

        return $redirect->with('success', 'Recipe updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Recipe $recipe
     * @return Response
     */
    public function destroy(Recipe $recipe)
    {
        //
    }

    protected function validateRecipe(Request $request, ?Recipe $recipe = null): array
    {
        $recipe ??= new Recipe();

        return $request->validate([
            'title'               => 'required',
            'slug'                => ['required', Rule::unique('recipes', 'slug')->ignore($recipe)],
            'image'               => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png'],
            'servings'            => ['required', 'min:1'],
            'preparation_minutes' => ['nullable', 'min:1'],
            'cooking_minutes'     => ['nullable', 'min:1'],
            'difficulty'          => 'nullable',
            'summary'             => 'nullable',
            'ingredients'         => 'required',
            'instructions'        => 'required',
            'source_label'        => 'nullable',
            'source_link'         => ['nullable', 'url'],
        ]);
    }

    protected function saveImage(Request $request): bool|string|null
    {
        if ($image = $request->file('image')) {
            // Add a timestamp to the image to prevent browser cache issues.
            $fileName = Str::slug($request->get('slug')) . '-' . Carbon::now()->getTimestamp() . '.' . $image->extension();
            $path     = $image->storePubliclyAs('public/images', $fileName);

            if (!$path) {
                Log::error('The recipe image could not be saved.', ['id' => $request->get('id')]);
                return false;
            }

            return $path;
        }

        return null;
    }

    protected function destroyImage(Recipe $recipe): bool
    {
        if (Storage::delete($recipe->image) === false) {
            Log::error('The recipe image could not be deleted.', ['id' => $recipe->id, 'image' => $recipe->image]);
            return false;
        }

        return true;
    }

}
