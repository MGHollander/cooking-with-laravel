<div class="relative" x-data="{
    showDropdown: false,
    availableLocales: ['en', 'nl'],
    localeNames: {
        'en': '{{ __('app.languages.en') }}',
        'nl': '{{ __('app.languages.nl') }}'
    }
}">
    <button type="button"
            class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500"
            :aria-label="'{{ __('app.languages.switch_language') }}'"
            @click="showDropdown = !showDropdown">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
        </svg>
    </button>

    <div x-show="showDropdown"
         x-cloak
         class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         @click.outside="showDropdown = false">
        <template x-for="locale in availableLocales" :key="locale">
            <form :action="'{{ route('locale.update') }}'" method="POST"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                @csrf
                <input type="hidden" name="locale" x-bind:value="locale">
                <button type="submit" class="w-full text-left flex items-center">
                    <span class="mr-2" x-text="locale === 'en' ? '🇬🇧' : '🇳🇱'"></span>
                    <span x-text="localeNames[locale]"></span>
                </button>
            </form>
        </template>
    </div>
</div>