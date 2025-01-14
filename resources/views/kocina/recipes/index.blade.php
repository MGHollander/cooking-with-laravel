<x-kocina.layout>
    <div class="container recipes-page">
        @if ($recipes->total() > 0)
            <div class="hero hero-plates-mirrored">
                <div class="hero-overlay"></div>
                <h1 class="hero-title">Ontdek mijn favoriete gerechten</h1>
                <p class="hero-text">
                    Mijn culinaire schatkamer, waar smaak en passie samenkomen! Hier vind je een
                    zorgvuldig samengestelde collectie van mijn meest geliefde recepten, stuk voor stuk
                    pareltjes die ik met veel plezier met je deel.
                </p>
            </div>

            <div class="recipes-grid">
                <h2 class="recipes-title">Recepten</h2>

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
            <p>Er zijn nog geen recepten toegevoegd.</p>

            @auth
                <a href="{{ route('recipes.create') }}" class="button button-primary">Voeg je eerste recept toe</a>
            @endauth
        @endif
    </div>
</x-kocina.layout>
