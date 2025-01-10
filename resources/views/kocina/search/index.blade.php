<x-kocina.layout>
    <div class="container page">
        <div class="search">
            <x-kocina.search-bar class="search-bar" :search-key="request('q')" />

            <h1 class="search-title">Vind nieuwe favoriete recepten</h1>

            <div @class(['search-subtitle', 'no-results' => $recipes->total() === 0])>
                @if ($recipes->total() > 0)
                    @if (request('q'))
                        @if ($recipes->total() === 1)
                            Is dit je nieuwe favoriet met <em>{{ request('q') }}</em>?
                        @else
                            tussen de {{ $recipes->total() }} recepten met <em>{{ request('q') }}</em>
                        @endif
                    @else
                        tussen de {{ $recipes->total() }} recepten in onze database
                    @endif
                @else
                    Geen recepten gevonden met <em>{{ request('q') }}</em>. Probeer een andere zoekterm of
                    <a href="{{ route('search') }}">blader door al onze recepten</a>.
                @endif
            </div>

            @if ($recipes->total() > 0)
                <div class="recipes-grid">
                    @foreach ($recipes->items() as $recipe)
                        <x-kocina.recipe-card :recipe="$recipe" title_tag="h2" />
                    @endforeach
                </div>
            @endif

            @if ($recipes->hasPages())
                <div>
                    {{-- TODO Test pagination with a lot of pages. --}}
                    {{ $recipes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-kocina.layout>
