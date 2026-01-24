<?php

if (! function_exists('route_recipe_show')) {
    function route_recipe_show(string $slug, string $locale): string
    {
        if ($locale === 'nl') {
            return route('recipes.show.nl', $slug);
        }

        return route('recipes.show.en', $slug);
    }
}
