<x-kocina.layout>
    <div class="container">
        <div class="hero hero-wine">
            <div class="hero-overlay"></div>
            <h1 class="hero-title">Ontdek mijn favoriete gerechten</h1>
            <p class="hero-text">
                Mijn culinaire schatkamer, waar smaak en passie samenkomen! Hier vind je een
                zorgvuldig samengestelde collectie van mijn meest geliefde recepten, stuk voor stuk
                pareltjes die ik met veel plezier met je deel.
            </p>
        </div>

        @if ($recipes->total() > 0)
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
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
            <p>Je hebt nog geen recepten opgeslagen.</p>
        @endif
    </div>
</x-kocina.layout>
