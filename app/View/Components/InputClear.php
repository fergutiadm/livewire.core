<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputClear extends Component
{
    public $placeholder;
    public $disabled;
    public $type;
    public $style;

    public function __construct($placeholder = '', $disabled = false, $type = 'text', ?string $style = null)
    {
        $this->placeholder = $placeholder;
        $this->disabled = $disabled;
        $this->type = $type;
        $this->style = $style;
    }

    public function render()
    {
        return view('components.input-clear');
    }
}
