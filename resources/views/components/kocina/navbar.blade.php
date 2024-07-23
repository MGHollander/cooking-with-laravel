@push('scripts')
    <script src="{{ Vite::asset('resources/kocina/js/navbar.js') }}"></script>
@endpush

<div id="navbar" class="navbar container" x-data="navbar" @keydown.escape="() => { if(open) toggleNav(); }">
    <div class="navbar-left">
        <a href="{{ route('home') }}" class="navbar-title">{{ env('APP_NAME') }}</a>
    </div>

    <div class="navbar-right">
        <button class="navbar-search-button" @click="toggleSearch">
            <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <circle fill="none" stroke="currentColor" stroke-width="1.5" cx="9" cy="9" r="7"></circle>
                <path fill="none" stroke="currentColor" stroke-width="1.5" d="M14,14 L18,18 L14,14 Z"></path>
            </svg>
        </button>

        <button
            aria-label="Menu uitklappen"
            class="navbar-nav-button"
            :class="{'navbar-nav-button-active' : open}"
            @click="toggleNav"
        >
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>

    <div class="navbar-search-bar" x-show="openSearch" x-collapse x-transition x-cloak x-trap="openSearch"
         @keydown.escape="() => { if(openSearch) openSearch = false; }">
        <x-kocina.search-bar>
            <button
                type="button"
                class="navbar-search-bar-close-button"
                @click="openSearch = false"
            >
                <span class="sr-only">Zoekveld sluiten</span>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 384 512"
                    x-transition
                >
                    <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                        d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
                </svg>
            </button>
        </x-kocina.search-bar>
    </div>

    <div
        x-cloak
        x-show=" open
    "
        x-bind="offcanvasOverlayTransition"
        x-on:click="toggle"
        class="navbar-menu-offcanvas-overlay"
    >
    </div>

    <nav
        x-cloak
        x-show="open"
        x-trap.noscroll="open"
        x-bind="offcanvasTransition"
        class="navbar-menu-offcanvas"
    >
        <ul>
            <li><a href="{{ route("home") }}">Home</a></li>
            {{-- TODO Add about me page --}}
            <li><a href="{{ route("home") }}">Over mij</a></li>
            <li><a href="{{ route("login") }}">Inloggen</a></li>
        </ul>
        <button class="navbar-menu-offcanvas-button" @click="toggleNav">
            <span class="sr-only">Menu inklappen</span>
        </button>
    </nav>
</div>

