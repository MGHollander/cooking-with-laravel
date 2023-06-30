<?php

namespace App\Http\Resources;

use Carbon\CarbonInterval;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ImportResource extends JsonResource
{
    public static $wrap;

    public function toArray($request): array
    {
        return [
            'title'               => $this->resource['title'],
            'image'               => !empty($this->resource['images']) ? $this->resource['images'][0] : null,
            'tags'                => $this->resource['keywords'],
            'preparation_minutes' => $this->transformTime($this->resource['prepTime']),
            'cooking_minutes'     => $this->transformTime($this->resource['cookTime'] ?? $this->resource['totalTime']),
            'servings'            => (int) $this->resource['yield'],
            'difficulty'          => 'average',
            'ingredients'         => is_array($this->resource['ingredients']) ? implode("\n", $this->resource['ingredients']) : $this->resource['ingredients'],
            'instructions'        => $this->transformInstructions($this->resource['steps']),
            'source_label'        => \Str::replace('www.', '', parse_url($this->resource['url'], PHP_URL_HOST)),
            'source_link'         => $this->resource['url'],
        ];
    }

    private function transformTime(?string $timeString): ?float
    {
        $prepTime = CarbonInterval::fromString($timeString)->totalMinutes;
        return $prepTime > 0 ? $prepTime : null;
    }

    private function transformInstructions(array $steps): string
    {
        if (count($steps) > 1) {
            $listSteps = true;

            // Loop through the step and check if there is a row that starts with <li>.
            // If so, we assume that the steps are already in a list.
            foreach ($steps as $step) {
                if (Str::startsWith($step, '<li>')) {
                    $listSteps = false;
                    break;
                }
            }

            if ($listSteps) {
                return '<ul><li>' . implode('</li><li>', $steps) . '</li></ul>';
            }

            return implode('', $steps);
        }

        return $steps[0];
    }
}
