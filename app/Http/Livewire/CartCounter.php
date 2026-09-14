<?php
namespace App\Http\Livewire;

use Livewire\Component;

class CartCounter extends Component
{
    public $count = 0;

    protected $listeners = ['productoAgregado' => 'incrementCount'];

    public function incrementCount()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
