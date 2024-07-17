<x-kocina.layout>
    <div class="container">
        @if ($recipes->total() > 0)
            <div class="hero hero-wine">
                <div class="hero-overlay"></div>
                <h1 class="hero-title">Ontdek mijn favoriete gerechten</h1>
                <p class="hero-text">
                    Mijn culinaire schatkamer, waar smaak en passie samenkomen! Hier vind je een
                    zorgvuldig samengestelde collectie van mijn meest geliefde recepten, stuk voor stuk
                    pareltjes die ik met veel plezier met je deel.
                </p>
            </div>

            <x-kocina.recipe-search/>

            <div class="recipes">
                @foreach ($recipes->items() as $recipe)
                    <x-kocina.recipe-card :recipe="$recipe"/>
                @endforeach
            </div>

            @if ($recipes->hasPages())
                <div class="mt-8">
                    {{ $recipes->links() }}
                </div>
            @endif
        @else
            {{-- TODO Add a better page when there are no recipes. --}}
            <p>Er zijn nog geen recepten toegevoegd.</p>

            @auth
                <a href="{{ route('recipes.create') }}" class="button button-primary">Voeg je eerste recept toe</a>
            @endauth
        @endif
    </div>
</x-kocina.layout>
