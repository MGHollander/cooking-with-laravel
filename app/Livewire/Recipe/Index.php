<?php

namespace App\Livewire\Recipe;

use App\Models\Recipe;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
  use WithPagination;

  public function render()
  {
    // TODO use a resource instead of a query
    return view('livewire.recipe.index', [
      'recipes' => Recipe::query()
        ->paginate(12)
        ->through(fn($recipe) => [
          'id'    => $recipe->id,
          'title' => $recipe->title,
          'slug'  => $recipe->slug,
          'image' => $recipe->getFirstMediaUrl('recipe_image', 'card'),
        ]),
    ]);
  }
}
