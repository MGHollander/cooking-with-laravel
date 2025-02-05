@props(['searchKey' => ''])

<div {{ $attributes->merge(['class' => 'search-bar']) }} x-data="{ searchKey: '{{ $searchKey }}' }">
    <form action="{{ route('search') }}" class="search-bar-form">
        {{--
          TODO Very low prio. See if I can fix the bug with the clear button.
            When you search from the bar and navigate back from the search page,
            then the clear button is not visible. Probably because Alpine.js does not know that
            the search field is filled. Maybe something to check on load or on init?
        --}}
        <input
            type="search"
            name="q"
            x-model="searchKey"
            x-ref="search"
            placeholder="Zoek een recept, ingredient, thema of keuken"
            class="search-bar-field"
        />

        <button
            type="button"
            class="search-bar-clear-button"
            @click="searchKey = '' || $refs.search.focus()"
            x-show="searchKey?.length > 0"
            x-transition
        >
            <span class="sr-only">Leeg het zoekveld</span>
            <x-icon.cross width="20" />
        </button>

        <button type="submit" class="search-bar-submit-button">
            <span class="sr-only">Zoeken</span>
            <x-icon.magnify-glass width="20" />
        </button>

        {{--
        <button type="button" class="search-bar-filter-button">
            <span class="sr-only">Open filters</span>
            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"/>
            </svg>
        </button>
        --}}

        {{ $slot }}
    </form>
</div>
