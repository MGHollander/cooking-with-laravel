@props([
  "recipe",
  "title_tag" => "h3",
])

<div class="recipe-card">
  <a href="{{ route_recipe_show($recipe["slug"], $recipe["locale"] ?? "nl") }}" class="recipe-card-link"{!! isset($recipe["no_index"]) && $recipe["no_index"] ? ' rel="nofollow"' : '' !!}>
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
    {{-- @formatter:on --}}
  </a>

  @auth
    <a href="{{ route("recipes.edit", $recipe["id"]) }}" class="recipe-card-edit">
      <x-icon.pen />
    </a>
  @endauth
</div>
