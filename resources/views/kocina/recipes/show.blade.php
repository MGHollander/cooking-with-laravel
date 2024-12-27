<x-kocina.layout>
    <x-slot name="title">
        {{ $recipe["title"] }}
    </x-slot>

    <x-slot name="meta">
        <link rel="canonical" href="{{ route("recipes.show", ["slug" => $recipe["slug"]]) }}" />
    </x-slot>

    @push("scripts")
        <script src="{{ Vite::asset('resources/kocina/js/recipe.js') }}"></script>
    @endpush

    <div
        class="container recipe-page"
        x-data="recipe({{ Illuminate\Support\Js::from($recipe["ingredients"]) }}, parseInt({{ $recipe["servings"] }}) ?? 1)"
    >
        <x-kocina.recipe.header :recipe="$recipe" />

        <div class="recipe-content-container">
            <x-kocina.recipe.ingredient-list :recipe="$recipe" />

            <div class="recipe-instructions-container space-y-4 sm:px-6 md:w-2/3 md:px-0">
                <h2 class="mb-4 text-xl font-bold md:mt-6 md:text-2xl">Instructies</h2>

                <div class="recipe-instructions">
                    {!! $recipe["instructions"] !!}
                </div>

                @if ($recipe["source_label"] || $recipe["source_link"])
                    <p>
                        <strong>Bron:</strong>
                        @if ($recipe["source_link"])
                            <a href="{{ $recipe["source_link"] }}" target="_blank">
                                {{ $recipe["source_label"] ?? $recipe["source_link"] }}
                            </a>
                        @elseif ($recipe["source_label"])
                            {{ $recipe["source_label"] }}
                        @endif
                    </p>
                @endif
            </div>

            @if (count($recipe["tags"]) > 0)
                <div class="recipe-tags-container">
                    @foreach ($recipe["tags"] as $tag)
                        <div>{{ $tag }}</div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-kocina.layout>
