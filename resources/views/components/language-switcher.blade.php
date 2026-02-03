<div class="navbar-language-group" x-data="{
    openLangDropdown: false,
    availableLocales: ['en', 'nl'],
    localeNames: {
        'en': '{{ __('app.languages.en') }}',
        'nl': '{{ __('app.languages.nl') }}'
    }
}">
    <button 
        class="navbar-button"
        :aria-label="'{{ __('app.languages.switch_language') }}'"
        @click="openLangDropdown = !openLangDropdown"
    >
        <x-icon.globe width="24" height="24" />
    </button>

    <nav 
        class="navbar-dropdown-menu"
        x-cloak
        x-show="openLangDropdown"
        x-trap="openLangDropdown"
        @click.outside="openLangDropdown = false"
        @keyup.esc="openLangDropdown = false"
        x-transition
    >
         <ul>
             <template x-for="locale in availableLocales" :key="locale">
                <li>
                    <form :action="'{{ route('locale.update') }}'" method="POST">
                        @csrf
                        <input type="hidden" name="locale" x-bind:value="locale">
                        <button type="submit" class="navbar-language-menu-item" :class="locale === '{{ app()->getLocale() }}' && 'navbar-language-menu-item-active'">
                            <template x-if="locale === 'en'">
                                <x-icon.flag-gb width="20" height="20" />
                            </template>
                            <template x-if="locale === 'nl'">
                                <x-icon.flag-nl width="20" height="20" />
                            </template>
                            <span x-text="localeNames[locale]"></span>
                        </button>
                    </form>
                </li>
            </template>
        </ul>
    </nav>
</div>