<ul {{ $attributes }}>
    <x-kocina.nav-list-item label="Recept toevoegen" :route="route('recipes.create', auth()->user())" />
    <x-kocina.nav-list-item label="Recept importeren" :route="route('import.index')" />
</ul>
