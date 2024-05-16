<?php

namespace App\Livewire\Recipe;

use App\Http\Resources\Recipe\IngredientsResource;
use App\Models\Recipe;
use Illuminate\Support\Collection;
use Livewire\Component;

class Show extends Component
{
  public Recipe $recipe;
  public string $image;
  public array $ingredients;
  public int $servings = 0;
  public Collection $tags;

  public function mount($slug)
  {
    // TODO use a resource.
    $this->recipe = Recipe::findBySlug($slug);
    $this->image  = $this->recipe->getFirstMediaUrl('recipe_image', 'show');
    // TODO I think this is not the way to go, but for the experiment it's fine.
    $this->ingredients = (new IngredientsResource(""))->transformIngredients($this->recipe->ingredients);
    $this->servings    = $this->recipe->servings;
    $this->tags        = $this->recipe->tags->pluck('name', 'id');
  }

  public function render()
  {
    return view('livewire.recipe.show');
  }
}
