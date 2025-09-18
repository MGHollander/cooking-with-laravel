<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function uploadExternalImage($url, ?string $imageName): bool|string
    {
        if (Http::get($url)->failed()) {
            throw new \Exception('The external image could not be found.');
        }

        $contents = file_get_contents($url);

        if (!$imageName) {
            $imageName = basename($url);
        }

        $image = 'images/recipes/' . $imageName;

        if (!Storage::disk('public')->put($image, $contents)) {
            Log::error('The external image could not be saved.', ['url' => $url]);
            return false;
        }

        return $image;
    }

    /**
     * Clean a URL by removing query parameters and fragments.
     */
    public static function cleanUrl(string $url): string
    {
        $parsedUrl = parse_url($url);

        if (!$parsedUrl) {
            return $url; // Return original if parsing fails
        }

        $cleanUrl = '';

        // Add scheme
        if (isset($parsedUrl['scheme'])) {
            $cleanUrl .= $parsedUrl['scheme'] . '://';
        }

        // Add host
        if (isset($parsedUrl['host'])) {
            $cleanUrl .= $parsedUrl['host'];
        }

        // Add port if present and not default
        if (isset($parsedUrl['port'])) {
            $cleanUrl .= ':' . $parsedUrl['port'];
        }

        // Add path
        if (isset($parsedUrl['path'])) {
            $cleanUrl .= $parsedUrl['path'];
        }

        return $cleanUrl;
    }
}
