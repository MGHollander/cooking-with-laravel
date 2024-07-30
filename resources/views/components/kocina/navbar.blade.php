@push('scripts')
    <script src="{{ Vite::asset('resources/kocina/js/navbar.js') }}"></script>
@endpush

<div id="navbar" class="navbar" x-data="navbar" @keyup.escape="if(openNav) toggleNav();">
    <div class="container navbar-container">
        <div class="navbar-left">
            <a href="{{ route('home') }}" class="navbar-title">{{ env('APP_NAME') }}</a>
            <nav class="navbar-menu-list">
                <x-kocina.nav-list/>
            </nav>
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
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z"
                                stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 14C8.13401 14 5 17.134 5 21H19C19 17.134 15.866 14 12 14Z"
                                  stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
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
                        <x-kocina.nav-list-user/>
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
        <x-kocina.nav-list/>

        <div class="navbar-menu-offcanvas-user">
            @guest
                <a href="{{ route('login') }}" class="button button-primary">Inloggen</a>
            @endguest

            @auth
                <x-kocina.nav-list-user/>
            @endauth
        </div>

        <button class="navbar-menu-offcanvas-button" @click="toggleNav">
            <span class="sr-only">Menu inklappen</span>
        </button>
    </nav>
</div>

