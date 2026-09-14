<?php
namespace App\View\Components;

use Illuminate\View\Component;

class InputFlatpickr extends Component
{
    public $label;
    public $placeholder;
    public $id;
    public $wireModel;

    public function __construct($label = null, $placeholder = '', $id = null, $wireModel = null)
    {
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->id = $id ?? 'flatpickr_' . uniqid();
        $this->wireModel = $wireModel;
    }

    public function render()
    {
        return view('components.input-flatpickr');
    }
}
