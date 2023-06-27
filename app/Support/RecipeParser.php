<?php

namespace App\Support;

use App\Http\Resources\ImportResource;
use Brick\StructuredData\Item;
use Illuminate\Support\Str;

class RecipeParser
{
    private array $recipeData = [
        'title'       => null,
        'url'         => null,
        'ingredients' => null,
        'steps'       => null,
        'yield'       => null,
        'prepTime'    => null,
        'cookTime'    => null,
        'totalTime'   => null,
        'images'      => null,
    ];

    public function __construct($url)
    {
        $this->recipeData['url'] = $url;
    }

    public static function fromItems($items, $url): ?ImportResource
    {
        foreach ($items as $item) {
            if (Str::contains(self::getTypesLower($item), 'recipe')) {
                return self::fromItem($item, $url);
            }
        }

        if (count($items) === 1) {
            return self::fromItem($items[0], $url);
        }

        return null;
    }

    private static function fromItem(Item $item, $url): ?ImportResource
    {
        return (new static($url))->parse($item);
    }

    public function parse(Item $item): ?ImportResource
    {
        foreach ($item->getProperties() as $name => $values) {
            $fn = 'parse_' . $this->sanitizeName($name);
            if (method_exists($this, $fn)) {
                $this->$fn($values);
            }
        }

        // We at least want a title.
        if (empty($this->recipeData['title'])) {
            return null;
        }

        return new ImportResource($this->recipeData);
    }

    private function parse_name($values): void
    {
        $this->recipeData['title'] = $this->getFirstValue($values);
    }

    private function parse_recipeyield($values): void
    {
        $this->recipeData['yield'] = $this->getFirstValue($values);
    }

    private function parse_preptime($values): void
    {
        $this->recipeData['prepTime'] = $this->getFirstValue($values);
    }

    private function parse_cooktime($values): void
    {
        $this->recipeData['cookTime'] = $this->getFirstValue($values);
    }

    private function parse_totaltime($values): void
    {
        $this->recipeData['totalTime'] = $this->getFirstValue($values);
    }

    private function parse_image($values): void
    {
        foreach ($values as $item) {
            if ($item instanceof Item) {
                $this->processItemImage($item);
            } elseif (is_string($item) && $this->isRelativeUrl($item)) {
                $this->addImage($item);
            } elseif (is_array($item)) {
                throw new \RuntimeException('Handle image items are array of strings');
            }
        }
    }

    private function parse_recipeingredient($values): void
    {
        if (is_array($values)) {
            $this->recipeData['ingredients'] = collect($values)->transform(function ($item) {
                return html_entity_decode($item);
            })->all();
        }
    }

    private function parse_recipeinstructions(array $items): void
    {
        foreach ($items as $item) {
            if ($item instanceof Item) {
                $this->processItem($item);
            } else {
                $this->recipeData['steps'][] = html_entity_decode($item);
            }
        }
    }

    private function processItem(Item $item): void
    {
        $types = self::getTypesLower($item);

        if (Str::contains($types, 'howtosection')) {
            $this->processHowToSection($item->getProperties());
        }

        if (Str::contains($types, 'howtostep')) {
            $this->processHowToStep($item->getProperties());
        }
    }

    private function processHowToSection(array $properties): void
    {
        foreach ($properties as $name => $values) {
            $name = $this->sanitizeName($name);

            if ($name === "name") {
                $this->recipeData['steps'][] = '<p><strong>' . html_entity_decode($values[0]) . '</strong></p>';
            }

            if ($name === "itemlistelement") {
                $this->processItemListElement($values);
            }
        }
    }

    private function processHowToStep(array $properties): void
    {
        foreach ($properties as $name => $values) {
            $name = $this->sanitizeName($name);

            if ($name === "text") {
                $this->recipeData['steps'][] = html_entity_decode($values[0]);
            }
        }
    }

    private function processItemListElement(array $values): void
    {
        $this->recipeData['steps'][] = '<ul>';

        foreach ($values as $value) {
            if ($value instanceof Item) {
                $this->processItemElement($value->getProperties());
            }
        }

        $this->recipeData['steps'][] = '</ul>';
    }

    private function processItemElement(array $properties): void
    {
        foreach ($properties as $name => $values) {
            $name = $this->sanitizeName($name);

            if ($name === "text") {
                $this->recipeData['steps'][] = '<li>' . html_entity_decode($values[0]) . '</li>';
            }
        }
    }

    private function processItemImage(Item $item): void
    {
        foreach ($item->getProperties() as $name => $values) {
            $name = $this->sanitizeName($name);

            if ($name === 'url' && $this->isRelativeUrl($values[0])) {
                $this->addImage($values[0]);
            }
        }
    }

    private static function getTypesLower(Item $item): string
    {
        return Str::lower(implode(',', $item->getTypes()));
    }

    private function addImage(string $url): void
    {
        $this->recipeData['images'][] = $url;
    }

    private function isRelativeUrl(string $value): bool
    {
        return Str::contains($value, ['http://', 'https://']);
    }

    private function getFirstValue($values)
    {
        return is_array($values) ? $values[0] : $values;
    }

    private function sanitizeName(string $name): string
    {
        return Str::replace(['http://schema.org/', 'https://schema.org/'], '', Str::lower($name));
    }
}
