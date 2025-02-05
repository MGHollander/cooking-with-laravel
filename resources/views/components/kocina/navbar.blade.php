@push('scripts')
    <script src="{{ Vite::asset('resources/kocina/js/components/navbar.js') }}" async></script>
@endpush

<div id="navbar" class="navbar" x-data="navbar" x-bind="events" :class="{ 'navbar-search-open' : openSearch }">
    <div class="container navbar-container">
        <div class="navbar-left">
            <a href="{{ route('home') }}" class="navbar-title">{{ env('APP_NAME') }}</a>
            <nav class="navbar-menu-list">
                <x-kocina.nav-list />
            </nav>
        </div>

        <div class="navbar-right">
            <button class="navbar-search-button" @click="toggleSearch()">
                <x-icon.magnify-glass width="24" height="24" />
            </button>

            <button
                aria-label="Menu uitklappen"
                class="navbar-nav-button"
                :class="{'navbar-nav-button-active' : openNav}"
                @click="toggleNav()"
            >
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>

            <div class="navbar-user-button-group">
                @guest
                    <a href="{{ route('login') }}" class="button button-primary">Inloggen</a>
                @endguest

                @auth
                    <button class="navbar-user-button" @click="toggleUserMenu()">
                        <x-icon.user width="24" height="24" />
                    </button>

                    <nav
                        class="navbar-user-menu"
                        x-cloak
                        x-show="openUserMenu"
                        x-trap="openUserMenu"
                        @click.outside="if(openUserMenu) toggleUserMenu()"
                        @keyup.esc="if(openUserMenu) toggleUserMenu()"
                        x-transition
                    >
                        <x-kocina.nav-list-user />
                    </nav>
                @endauth
            </div>
        </div>

        <div
            class="navbar-search-bar"
            x-cloak
            x-collapse
            x-show="openSearch"
            x-transition
            x-trap="openSearch"
            @keyup.escape="if(openSearch) openSearch = false"
        >
            <x-kocina.search-bar>
                <button
                    type="button"
                    class="navbar-search-bar-close-button"
                    @click="openSearch = false"
                >
                    <span class="sr-only">Zoekveld sluiten</span>
                    <x-icon.cross width="24" height="24" />
                </button>
            </x-kocina.search-bar>
        </div>
    </div>

    <div
        x-cloak
        x-show="openNav"
        x-bind="offcanvasOverlayTransition"
        @click="toggleNav"
        class="navbar-menu-offcanvas-overlay"
    >
    </div>

    <nav
        x-cloak
        x-show="openNav"
        x-trap.noscroll="openNav"
        x-bind="offcanvasTransition"
        class="navbar-menu-offcanvas"
    >
        <x-kocina.nav-list />

        <div class="navbar-menu-offcanvas-user">
            @guest
                <a href="{{ route('login') }}" class="button button-primary">Inloggen</a>
            @endguest

            @auth
                <x-kocina.nav-list-user />
            @endauth
        </div>

        <button class="navbar-menu-offcanvas-button" @click="toggleNav">
            <span class="sr-only">Menu inklappen</span>
        </button>
    </nav>
</div>

