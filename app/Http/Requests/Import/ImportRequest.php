<?php

namespace App\Http\Requests\Import;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    public function validationData(): array
    {
        return $this->query();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'url' => ['required', 'url'],
            'parser' => ['required', 'string', 'in:auto,structured-data,open-ai,firecrawl'],
            'force_import' => ['sometimes', 'string', 'in:true,false'],
        ];
    }
}
