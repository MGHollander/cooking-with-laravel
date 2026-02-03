<ul {{ $attributes }}>
    <x-kocina.nav-list-item label="{{ __('nav.home') }}" :route="route('home')" />
    <x-kocina.nav-list-item label="{{ __('nav.about') }}" :route="route('about-me.' . app()->getLocale())" />
</ul>
