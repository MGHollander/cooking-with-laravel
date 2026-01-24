<?php

namespace App\Support;

/**
 * Centralized helper for image type configuration and validation.
 *
 * This class provides a single interface to access supported image types
 * configured in config/media-library.php. It converts between different
 * formats needed throughout the application:
 *
 * - Laravel validation rules (mimes:jpg,png,webp,avif)
 * - HTML accept attributes (image/jpeg,image/png,...)
 * - PHP IMAGETYPE_* constants for getimagesize() validation
 * - Human-readable lists for error messages
 *
 * Usage examples:
 * - RecipeRequest validation: ImageTypeHelper::getMimes()
 * - Vue.js accept attribute: config.supported_mime_types
 * - ExternalImage rule: ImageTypeHelper::getImageTypeConstants()
 * - Error messages: ImageTypeHelper::getHumanReadableList()
 */
class ImageTypeHelper
{
    /**
     * Get image extensions for Laravel mimes validation (e.g., "jpg,png,webp,avif")
     *
     * Used in: RecipeRequest validation rules
     */
    public static function getMimes(string $context = 'recipe'): string
    {
        $types = config("media-library.supported_image_types.{$context}", []);

        return collect($types)->pluck('extension')->implode(',');
    }

    /**
     * Get PHP IMAGETYPE_* constants for validation
     *
     * Used in: ExternalImage rule, ImportController::filterValidImages()
     */
    public static function getImageTypeConstants(string $context = 'recipe'): array
    {
        $types = config("media-library.supported_image_types.{$context}", []);

        return collect($types)->pluck('imagetype_constant')->values()->toArray();
    }

    /**
     * Get mime types for HTML accept attribute (e.g., "image/jpeg,image/png,...")
     *
     * Used in: Vue.js file input accept attributes (via Inertia config)
     */
    public static function getMimeTypes(string $context = 'recipe'): string
    {
        $types = config("media-library.supported_image_types.{$context}", []);

        return collect($types)->pluck('mime_type')->implode(',');
    }

    /**
     * Get human-readable list (e.g., "AVIF, JPEG, PNG and WEBP")
     *
     * Used in: Validation error messages with locale support
     */
    public static function getHumanReadableList(string $context = 'recipe', string $locale = 'en'): string
    {
        $types = config("media-library.supported_image_types.{$context}", []);
        $names = collect($types)->pluck('display_name')->values();

        if ($names->count() === 0) {
            return '';
        }

        if ($names->count() === 1) {
            return $names->first();
        }

        return $names->implode(', ');
    }

    /**
     * Get all supported extensions
     */
    public static function getExtensions(string $context = 'recipe'): array
    {
        $types = config("media-library.supported_image_types.{$context}", []);

        return collect($types)->pluck('extension')->values()->toArray();
    }
}
