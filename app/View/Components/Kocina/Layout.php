<?php

namespace App\View\Components\Kocina;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function render(): View
    {
        return view('kocina.layout');
    }
}
