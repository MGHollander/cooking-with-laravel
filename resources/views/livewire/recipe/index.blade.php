<div>
  <h1 class="mb-4 text-2xl font-bold md:text-3xl">Recipes</h1>

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
  @else
    <p>Je hebt nog geen recepten opgeslagen.</p>
  @endif
</div>
