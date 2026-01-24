<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use LanguageDetection\Language;

class LanguageDetectionService
{
    private Language $detector;

    public function __construct()
    {
        $this->detector = new Language;
    }

    public function detectLanguage(?string $title = null, ?string $summary = null, ?string $ingredients = null, ?string $instructions = null): ?string
    {
        $textParts = [];

        if ($title) {
            $textParts[] = $title;
        }

        if ($summary) {
            $textParts[] = $summary;
        }

        if ($ingredients) {
            $textParts[] = strip_tags($ingredients);
        }

        if ($instructions) {
            $textParts[] = strip_tags($instructions);
        }

        if (empty($textParts)) {
            return null;
        }

        $combinedText = implode(' ', $textParts);

        if (strlen(trim($combinedText)) < 10) {
            return null;
        }

        try {
            $detected = $this->detector->detect($combinedText)->close();

            if (empty($detected)) {
                return null;
            }

            $topLanguage = array_key_first($detected);

            Log::debug('Language detection result', [
                'detected' => $topLanguage,
                'confidence' => $detected[$topLanguage] ?? null,
                'text_length' => strlen($combinedText),
            ]);

            return $topLanguage;
        } catch (\Exception $e) {
            Log::warning('Language detection failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
