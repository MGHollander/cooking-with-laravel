<x-blade.layout>
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold md:text-3xl">
      @if (request("search") && $recipes->total() > 0)
        Found {{ $recipes->count() }} recipes for "{{ request("search") }}"
      @elseif (request("search") && $recipes->total() === 0)
        No recipes found for "{{ request("search") }}"
      @else
        Recipes
      @endif
    </h1>
    <form class="flex items-center space-x-2">
      <label>Search:</label>
      <input
        type="search"
        name="search"
        value="{{ request("search") }}"
        class="rounded border border-slate-300 px-3 py-2"
      />
      <button type="submit" class="rounded bg-slate-300 px-3 py-2">Search</button>
    </form>
  </div>
  @if ($recipes->total() > 0)
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      @foreach ($recipes->items() as $recipe)
        <x-blade.recipe-card :recipe="$recipe" />
      @endforeach
    </div>

    @if ($recipes->hasPages())
      <div class="mt-8">
        {{ $recipes->links() }}
      </div>
    @endif
  @elseif (request("search") && $recipes->total() === 0)
    <p>No recipes found for "{{ request("search") }}".</p>
  @else
    <p>No recipes found.</p>
  @endif
</x-blade.layout>
