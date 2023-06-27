<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function uploadExternalImage($url, ?string $imageName): bool|string
    {
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
}
