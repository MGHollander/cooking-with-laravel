<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'public_url' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'public_url')->ignore($this->user()->id),
            ],
            'default_language' => [
                'required',
                Rule::in(['nl', 'en']),
            ],
        ];
    }
}
