@push('scripts')
    <script src="{{ Vite::asset('resources/kocina/js/navbar.js') }}"></script>
@endpush

<div id="navbar" class="navbar container" x-data="navbar" @keydown.escape="() => { if(open) toggle(); }">
    <div class="navbar-left">
        <a href="{{ route('home') }}" class="navbar-title">{{ env('APP_NAME') }}</a>
    </div>
    <div class="navbar-right">
        <button class="navbar-search-button">
            <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <circle fill="none" stroke="#000" stroke-width="1.1" cx="9" cy="9" r="7"></circle>
                <path fill="none" stroke="#000" stroke-width="1.1" d="M14,14 L18,18 L14,14 Z"></path>
            </svg>
        </button>
        <button
            aria-label="Menu uitklappen"
            class="navbar-nav-button"
            :class="{'navbar-nav-button-active' : open}"
            @click="toggle"
        >
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
    <div
        x-cloak
        x-show="open"
        x-bind="offcanvasOverlayTransition"
        x-on:click="toggle"
        class="navbar-menu-offcanvas-overlay"
    ></div>
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
        <button class="navbar-menu-offcanvas-button" @click="toggle">
            <span class="sr-only">Menu inklappen</span>
        </button>
    </nav>
</div>

