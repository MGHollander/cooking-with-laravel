<x-kocina.layout>
    <div class="container page">
        <div class="search">
            <x-kocina.search-bar class="search-bar" :search-key="request('q')" />

            <h1 class="search-title">{{ __('recipes.search.title') }}</h1>

            <div @class(['search-subtitle', 'no-results' => $recipes->total() === 0])>
                @if ($recipes->total() > 0)
                    @if (request('q'))
                        @if ($recipes->total() === 1)
                            {{ __('recipes.search.subtitle_single', ['query' => request('q')]) }}
                        @else
                            {{ __('recipes.search.subtitle_multiple', ['count' => $recipes->total(), 'query' => request('q')]) }}
                        @endif
                    @else
                        {{ __('recipes.search.subtitle_all', ['count' => $recipes->total()]) }}
                    @endif
                @else
                    {{ __('recipes.search.no_results', ['query' => request('q'), 'browse_link' => '<a href="' . route('search') . '">' . __('recipes.search.browse') . '</a>']) }}
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
                <x-kocina.pagination :paginator="$recipes" />
            @endif
        </div>
    </div>
</x-kocina.layout>
