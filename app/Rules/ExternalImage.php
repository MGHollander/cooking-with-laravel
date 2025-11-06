<?php

namespace App\Rules;

use App\Support\ImageTypeHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ExternalImage implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $externalImage = Http::get($value);
        if ($externalImage->failed()) {
            $fail('validation.custom.external_image.not_found')->translate();

            return;
        }

        try {
            $imageInfo = getimagesizefromstring($externalImage->body());

            if ($imageInfo === false || ! in_array($imageInfo[2], ImageTypeHelper::getImageTypeConstants(), true)) {
                $fail('validation.custom.external_image.invalid_type', [
                    'supported_types' => ImageTypeHelper::getHumanReadableList('recipe', app()->getLocale()),
                ])->translate();

                return;
            }
        } catch (\Throwable $e) {
            $fail('validation.custom.external_image.invalid_type', [
                'supported_types' => ImageTypeHelper::getHumanReadableList('recipe', app()->getLocale()),
            ])->translate();

            return;
        }
    }
}
