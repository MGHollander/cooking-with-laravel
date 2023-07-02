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
            'image'               => isset($this->resource['images']) && is_array($this->resource['images']) ? $this->resource['images'][0] : $this->resource['images'] ?? null,
            'tags'                => $this->resource['keywords'] ?? null,
            'preparation_minutes' => isset($this->resource['prepTime']) ? $this->transformTime($this->resource['prepTime']) : null,
            'cooking_minutes'     => isset($this->resource['cookTime']) || isset($this->resource['totalTime']) ? $this->transformTime($this->resource['cookTime'] ?? $this->resource['totalTime']) : null,
            'servings'            => (int) $this->resource['yield'],
            'difficulty'          => $this->resource['difficulty'] ?? 'average',
            'ingredients'         => isset($this->resource['ingredients']) && is_array($this->resource['ingredients']) ? implode("\n", $this->resource['ingredients']) : $this->resource['ingredients'] ?? null,
            'instructions'        => isset($this->resource['steps']) ? $this->transformInstructions($this->resource['steps']) : null,
            'source_label'        => isset($this->resource['url']) ? Str::replace('www.', '', parse_url($this->resource['url'], PHP_URL_HOST)) : null,
            'source_link'         => $this->resource['url'] ?? null,
        ];
    }

    private function transformTime(?string $timeString): ?float
    {
        try {
            $prepTime = CarbonInterval::fromString($timeString)->totalMinutes;
            return $prepTime > 0 ? $prepTime : null;
        } catch (\Exception $e) {
        }

        return (int) $timeString;
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
