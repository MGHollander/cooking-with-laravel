@props([
  "recipe",
  "title_tag" => "h3",
])

<a href="{{ route("recipes.show", ["slug" => $recipe["slug"]]) }}" class="recipe-card recipe-card-link">
    <div class="recipe-card-media">
        @if ($recipe["image"])
            <img
                src="{{ $recipe["image"] }}"
                alt="{{ $recipe["title"] }}"
                class="recipe-card-image"
                width="300"
                height="160"
            />
        @else
            <x-icon.meal class="recipe-card-image-placeholder" />
        @endif
    </div>

    {{-- @formatter:off --}}
    <{{ $title_tag }} class="recipe-card-title">{{ $recipe["title"] }}</{{ $title_tag }}>
</a>
{{-- @formatter:on --}}
