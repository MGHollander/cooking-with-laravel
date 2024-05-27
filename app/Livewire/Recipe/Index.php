<?php

namespace App\Livewire\Recipe;

use App\Models\Recipe;
use Livewire\Component;
use Livewire\WithPagination;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class Index extends Component
{
  use WithPagination;

  public ?string $search = null;

  public function render()
  {
    // TODO use a resource instead of a query
    return view('livewire.recipe.index', [
      'recipes' => Search::add(Recipe::class, ['title', 'ingredients', 'instructions', 'tags.name'])
        ->paginate(12)
        ->beginWithWildcard()
        ->search($this->search)
        ->withQueryString()
        ->through(fn($recipe) => [
          'id'    => $recipe->id,
          'title' => $recipe->title,
          'slug'  => $recipe->slug,
          'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
        ]),
      // TODO Add open graph tags.
    ]);
  }
}
