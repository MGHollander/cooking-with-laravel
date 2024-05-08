<?php

namespace App\View\Components\Blade;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
  public function render(): View
  {
    return view('blade.layout');
  }
}
