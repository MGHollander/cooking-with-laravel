<div {{ $attributes->merge(['class' => 'search-bar']) }} x-data="{ search: '' }">
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
            x-model="search"
            placeholder="Zoek een recept, ingredient, thema of keuken"
            class="search-bar-field"
        />

        <button
            type="button"
            class="search-bar-clear-button"
            @click="search = ''"
            x-show="search.length > 0"
            x-transition
        >
            <span class="sr-only">Leeg het zoekveld</span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                viewBox="0 0 384 512"
                x-show="search.length > 0"
                x-transition
            >
                <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
            </svg>
        </button>

        <button type="submit" class="search-bar-submit-button">
            <span class="sr-only">Zoeken</span>
            <svg width="16" height="16" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle>
                <path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path>
            </svg>
        </button>

        {{--
        TODO Uncomment when filters are added.
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
