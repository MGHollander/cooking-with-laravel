<x-slot name="meta">
  <link rel="canonical" href="{{ route("home") }}" />
</x-slot>

<div>
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold md:text-3xl">
      @if ($search && $recipes->total() > 0)
        Found {{ $recipes->count() }} recipes for "{{ $search }}"
      @elseif ($search && $recipes->total() === 0)
        No recipes found
      @else
        Recipes
      @endif
    </h1>
    <form class="flex items-center space-x-2">
      <label>Search:</label>
      <input type="search" wire:model.live.debounce="search" class="rounded border border-slate-300 px-3 py-2" />
    </form>
  </div>

  @if ($recipes->total() > 0)
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      @foreach ($recipes->items() as $recipe)
        <livewire:components.recipe-card :$recipe :key="$recipe['id']" />
      @endforeach
    </div>

    @if ($recipes->hasPages())
      <div class="mt-8">
        {{ $recipes->links() }}
      </div>
    @endif
  @elseif ($search && $recipes->total() === 0)
    <p>No recipes found for "{{ $search }}".</p>
  @else
    <p>No recipes found.</p>
  @endif
</div>
