<x-kocina.layout>
    <div class="container page">
        <div class="search">
            <h1 class="search-title">Helaas, dit recept bestaat (nog) niet!</h1>

            <p>Gebruik de zoekbalk om je nieuwste favoriete recept te ontdekken.</p>

            <x-kocina.search-bar class="search-bar" />

            @php
                $total = $recipes->total();
                $oneRecipe = $total === 1;
            @endphp

            @if ($total > 0)
                <div>
                    <p>
                        Met behulp van de woorden uit de link {{ $oneRecipe ? 'is' : 'zijn' }} er
                        <strong>{{ $total }} {{ $oneRecipe ? 'recept' : 'recepten' }}</strong> gevonden
                        {{ $oneRecipe ? 'dat' : 'die' }} mogelijk toch interessant {{ $oneRecipe ? 'is' : 'zijn' }}.
                        Hiervoor zijn de volgende woorden gebruikt: <strong>{{ $searchKey }}</strong>
                    </p>

                    <p>Hopelijk zit er iets tussen!</p>
                </div>

                <div class="recipes">
                    @foreach ($recipes->items() as $recipe)
                        <x-kocina.recipe-card :recipe="$recipe" title_tag="h2" />
                    @endforeach
                </div>
            @endif

            @if ($recipes->hasPages())
                <div>
                    {{-- TODO Test pagination with a lot of pages --}}
                    {{-- TODO Make a reusable component for the pagination --}}
                    {{ $recipes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-kocina.layout>
