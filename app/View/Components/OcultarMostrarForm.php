<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class OcultarMostrarForm extends Component
{
    public $formularioVisible;
    /**
     * Create a new component instance.
     */
    public function __construct($formularioVisible = true)
    {
        $this->formularioVisible = $formularioVisible;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ocultar-mostrar-form');
    }
}
