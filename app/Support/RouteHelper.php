<?php

if (! function_exists('route_recipe_show')) {
    function route_recipe_show(string $publicId, ?string $slug, string $locale): string
    {
        $routeName = $locale === 'nl' ? 'recipes.show.nl' : 'recipes.show.en';

        return $slug
            ? route($routeName, [$publicId, $slug])
            : route($routeName, [$publicId]);
    }
}
