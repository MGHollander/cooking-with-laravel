<?php

namespace App\Rules;

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

        $imageType = exif_imagetype($value);
        if (! in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
            $fail('validation.custom.external_image.invalid_type')->translate();
        }
    }
}
