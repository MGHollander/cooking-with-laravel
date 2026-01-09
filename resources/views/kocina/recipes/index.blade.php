<x-kocina.layout>
  <div class="container recipes-page">
    @if ($recipes->total() > 0)
      <div class="hero hero-plates-mirrored">
        <div class="hero-overlay"></div>
        <h1 class="hero-title">{{ __('recipes.index.hero_title') }}</h1>
        <p class="hero-text">
          {{ __('recipes.index.hero_text') }}
        </p>
      </div>

      <div class="recipes-grid">
        <h2 class="recipes-title">{{ __('recipes.index.title') }}</h2>

        <x-kocina.search-bar class="recipes-search" />
      </div>

      <div class="recipes-grid">
        @foreach ($recipes->items() as $recipe)
          <x-kocina.recipe-card :recipe="$recipe" />
        @endforeach
      </div>

      @if ($recipes->hasPages())
        <x-kocina.pagination :paginator="$recipes" />
      @endif
    @else
      <p>{{ __('recipes.no_recipes') }}</p>

      @auth
        <a href="{{ route("recipes.create") }}" class="button button-primary">{{ __('recipes.add_first') }}</a>
      @endauth
    @endif
  </div>
</x-kocina.layout>
