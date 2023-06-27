<?php

namespace App\Http\Traits;

use App\Http\Requests\Recipe\RecipeRequest;
use App\Models\Recipe;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadImageTrait
{

    protected function saveImage(RecipeRequest $request): bool|string|null
    {
        if ($image = $request->file('image')) {

            // Add a timestamp to the image to prevent browser cache issues.
            $fileName = Str::slug($request->get('title')) . '-' . time() . '.' . $image->extension();
            $path     = $image->storePubliclyAs('images/recipes', $fileName, ['disk' => 'public']);

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
        if (Storage::disk('public')->delete($recipe->image) === false) {
            Log::error('The recipe image could not be deleted.', ['id' => $recipe->id, 'image' => $recipe->image]);
            return false;
        }

        return true;
    }

}
