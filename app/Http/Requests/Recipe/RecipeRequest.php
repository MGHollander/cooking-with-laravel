<?php

namespace App\Http\Requests\Recipe;

use App\Rules\ExternalImage;
use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'media' => ['nullable', 'image', 'mimes:jpg,png', 'max:'.(config('media-library.max_file_size', 1024 * 1024 * 4) / 1024)],
            'external_image' => ['nullable', 'url', new ExternalImage],
            'tags' => ['nullable', 'string'],
            'servings' => ['required', 'integer', 'min:1'],
            'preparation_minutes' => ['nullable', 'integer', 'min:1'],
            'cooking_minutes' => ['nullable', 'integer', 'min:1'],
            'difficulty' => 'required',
            'summary' => 'nullable',
            'ingredients' => 'required',
            'instructions' => 'required',
            'source_label' => 'nullable',
            'source_link' => ['nullable', 'url'],
            'import_log_id' => ['nullable', 'integer', 'exists:import_logs,id'],
            'return_to_import_page' => ['nullable', 'boolean'],
            'no_index' => ['nullable', 'boolean'],
        ];
    }
}
