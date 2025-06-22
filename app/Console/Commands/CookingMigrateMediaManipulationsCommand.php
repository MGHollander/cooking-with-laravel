<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CookingMigrateMediaManipulationsCommand extends Command
{
    protected $signature = 'cooking:migrate-media-manipulations';

    protected $description = 'Migrate media manipulations to the new format';

    public function handle(): void
    {
        $media = Media::where('collection_name', '=', 'recipe_image');
        $bar   = $this->output->createProgressBar($media->count());

        $bar->start();

        $media->chunk(100, function ($mediaItems) use ($bar) {
            $mediaItems->each(function (Media $media) use ($bar) {
                $updated       = false;
                $manipulations = $media->manipulations;
                if (isset($manipulations['card']['manualCrop']) && is_string($manipulations['card']['manualCrop'])) {
                    $manipulations['card']['manualCrop'] = array_map('intval', explode(',', $manipulations['card']['manualCrop']));
                    $manipulations['card']['width'] = [600];

                    $updated = true;
                }

                if (isset($manipulations['show']['manualCrop']) && is_string($manipulations['show']['manualCrop'])) {
                    $manipulations['show']['manualCrop'] = array_map('intval', explode(',', $manipulations['show']['manualCrop']));
                    $manipulations['show']['width'] = [1200];

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
