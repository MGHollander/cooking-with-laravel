<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CookingResizeRecipeImagesCommand extends Command
{
    protected $signature = 'cooking:resize-recipe-images';

    protected $description = 'This is a one-time command to resize all recipe images without manipulations to the new dimensions.';

    public function handle(): void
    {
        $this->info('First run media:regenerate to ensure all images are converted to webp');
        $this->newLine();
        $this->call('media:regenerate');
        $this->newLine();
        $this->info('Resize all images to the new dimensions');
        $this->newLine();
        $this->resizeImages();
        $this->newLine();
        $this->info('Done!');
        $this->newLine();
        $this->info('Update media manipulations');
        $this->newLine();
        $this->updateMediaManipulations();
        $this->newLine();
        $this->info('Done!');
        $this->newLine();
        $this->newLine();
        $this->info('Finished!');
    }

    private function resizeImages(): void
    {
        $media = Media::where('collection_name', '=', 'recipe_image')
            ->where('manipulations', '=', '[]');
        $bar = $this->output->createProgressBar($media->count());

        $bar->start();

        $media->chunk(100, function ($mediaItems) use ($bar) {
            $mediaItems->each(function (Media $media) use ($bar) {
                $path = $media->getPath('card');
                $width = config('media-library.image_dimensions.recipe.conversions.card.width');
                $height = config('media-library.image_dimensions.recipe.conversions.card.height');

                Image::load($path)
                    ->width($width)
                    ->fit(Fit::Crop, $width, $height)
                    ->save();

                $path = $media->getPath('show');
                $width = config('media-library.image_dimensions.recipe.conversions.show.width');
                $height = config('media-library.image_dimensions.recipe.conversions.show.height');

                Image::load($path)
                    ->width($width)
                    ->fit(Fit::Crop, $width, $height)
                    ->save();

                $bar->advance();
            });
        });

        $bar->finish();
    }

    private function updateMediaManipulations(): void
    {
        $media = Media::where('collection_name', '=', 'recipe_image');
        $bar = $this->output->createProgressBar($media->count());

        $bar->start();

        $media->chunk(100, function ($mediaItems) use ($bar) {
            $mediaItems->each(function (Media $media) use ($bar) {
                $manipulations = $media->manipulations;
                $updated = false;

                if (isset($manipulations['card']['manualCrop'])) {
                    $manipulations['card']['width'] = [config('media-library.image_dimensions.recipe.conversions.card.width')];
                    $updated = true;
                }

                if (isset($manipulations['show']['manualCrop'])) {
                    $manipulations['show']['width'] = [config('media-library.image_dimensions.recipe.conversions.show.width')];
                    $updated = true;
                }

                if ($updated) {
                    $media->manipulations = $manipulations;
                    $media->save();
                }

                $bar->advance();
            });
        });

        $bar->finish();
    }
}
