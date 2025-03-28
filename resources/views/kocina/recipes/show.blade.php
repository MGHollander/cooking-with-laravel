<x-kocina.layout>
    <x-slot name="title">
        {{ $recipe["title"] }}
    </x-slot>

    <x-slot name="meta">
        <link rel="canonical" href="{{ route("recipes.show", ["slug" => $recipe["slug"]]) }}" />
    </x-slot>

    <div
        class="container recipe-page"
        x-data="recipe({{ Illuminate\Support\Js::from($recipe["ingredients"]) }}, parseInt({{ $recipe["servings"] }}) ?? 1)"
    >
        <x-kocina.recipe.header :recipe="$recipe" />

        <x-kocina.recipe.cooking-setting :recipe="$recipe" />

        <div class="recipe-content-container">
            <x-kocina.recipe.ingredients :recipe="$recipe" />

            <x-kocina.recipe.instructions :recipe="$recipe" />

            @if (count($recipe["tags"]) > 0)
                <x-kocina.recipe.tags :recipe="$recipe" />
            @endif
        </div>
    </div>
</x-kocina.layout>
