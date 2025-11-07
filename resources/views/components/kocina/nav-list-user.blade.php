<ul {{ $attributes }}>
    <x-kocina.nav-list-item label="{{ __('nav.edit_profile') }}" :route="route('users.edit', auth()->user())"/>
    <x-kocina.nav-list-item label="{{ __('nav.change_password') }}" :route="route('users.password.edit')"/>
    <x-kocina.nav-list-item label="{{ __('nav.manage_users') }}" :route="route('users.index')"/>
    <li>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit">{{ __('nav.logout') }}</button>
        </form>
    </li>
</ul>
