<x-kocina.layout :locale="$recipe['locale']">
    <x-slot name="title">
        {{ $recipe["title"] }}
    </x-slot>

    <x-slot name="meta">
        @if($recipe["no_index"])
            <meta name="robots" content="noindex" />
        @endif
        
        <link rel="canonical" href="{{ $canonical_url }}" />
        
        @foreach($alternate_urls ?? [] as $altLocale => $altUrl)
            <link rel="alternate" hreflang="{{ $altLocale }}" href="{{ $altUrl }}" />
        @endforeach
        
        @if(isset($alternate_urls) && !empty($alternate_urls))
            <link rel="alternate" hreflang="x-default" href="{{ $canonical_url }}" />
        @endif
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
