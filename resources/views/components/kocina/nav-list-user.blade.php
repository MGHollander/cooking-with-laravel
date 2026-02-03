<ul {{ $attributes }}>
    <x-kocina.nav-list-item label="{{ __('nav.edit_profile') }}" :route="route('users.edit.' . app()->getLocale(), auth()->user())"/>
    <x-kocina.nav-list-item label="{{ __('nav.change_password') }}" :route="route('users.password.edit.' . app()->getLocale())"/>
    <x-kocina.nav-list-item label="{{ __('nav.manage_users') }}" :route="route('users.index.' . app()->getLocale())"/>
    <li>
        <form method="post" action="{{ route('logout.' . app()->getLocale()) }}">
            @csrf
            <button type="submit">{{ __('nav.logout') }}</button>
        </form>
    </li>
</ul>
