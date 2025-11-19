<?php

if (! function_exists('route_recipe_show')) {
    function route_recipe_show(string $slug, string $locale): string
    {
        $validLocales = ['en', 'nl'];
        $locale = in_array($locale, $validLocales) ? $locale : 'en';
        
        $routeName = $locale === 'en' ? 'recipes.show.en' : 'recipes.show.nl';
        return route($routeName, $slug);
    }
}

