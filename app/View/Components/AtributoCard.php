<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AtributoCard extends Component
{
    public array $card = [];
    public int|string $atributoId;

    public function __construct(int|string $atributoId = 0)
    {
        $this->atributoId = $atributoId;
    }

    public function render()
    {
        return view('components.atributo-card');
    }
}
