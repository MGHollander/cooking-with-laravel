<ul {{ $attributes }}>
    <x-kocina.nav-list-item label="{{ __('nav.add_recipe') }}" :route="route('recipes.create', auth()->user())" />
    <x-kocina.nav-list-item label="{{ __('nav.import_recipe') }}" :route="route('import.index')" />
</ul>
