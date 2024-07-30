<ul {{ $attributes }}>
    <x-kocina.nav-list-item label="Bewerk je profiel" :route="route('users.edit', auth()->user())"/>
    <x-kocina.nav-list-item label="Wijzig je wachtwoord" :route="route('users.password.edit')"/>
    <x-kocina.nav-list-item label="Beheer gebruikers" :route="route('users.index')"/>
    <li>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Uitloggen</button>
        </form>
    </li>
</ul>
