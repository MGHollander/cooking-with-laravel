<?php

namespace App\View\Components\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
  public function render(): View
  {
    return view('livewire.layout');
  }
}
