<?php

namespace App\Livewire\Components;

use Livewire\Component;

class RecipeCard extends Component
{
  public $recipe;

  public function render()
  {
    return view('livewire.components.recipe-card');
  }
}
