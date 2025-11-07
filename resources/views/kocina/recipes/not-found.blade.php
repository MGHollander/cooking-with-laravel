<x-kocina.layout>
    <div class="container page">
        <div class="search">
            <h1 class="search-title">{{ __('recipes.not_found.title') }}</h1>

            <p>{{ __('recipes.not_found.description') }}</p>

            <x-kocina.search-bar class="search-bar" />

            @php
                $total = $recipes->total();
                $oneRecipe = $total === 1;
            @endphp

            @if ($total > 0)
                <div>
                    <p>
                        {!! trans_choice('recipes.not_found.found', $total, [
                            'count' => $total
                        ]) !!}
                        {!! trans_choice('recipes.not_found.words_used', count($searchWords), [
                            'words' => implode(', ', $searchWords)
                        ]) !!}
                    </p>

                    <p>{{ __('recipes.not_found.hopefully') }}</p>
                 </div>

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
