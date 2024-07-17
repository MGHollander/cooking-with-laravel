@props([
  "recipe",
])

<a href="{{ route("recipes.show", ["slug" => $recipe["slug"]]) }}" class="recipe-card recipe-card-link">
    <div class="recipe-card-media">
        {{-- TODO Lazy load images --}}
        @if ($recipe["image"])
            <img
                src="{{ $recipe["image"] }}"
                alt="{{ $recipe["title"] }}"
                class="recipe-card-image"
            />
        @else
            <x-icon.meal class="recipe-card-image-placeholder"/>
        @endif
    </div>

    <h2 class="recipe-card-title">{{ $recipe["title"] }}</h2>
</a>
