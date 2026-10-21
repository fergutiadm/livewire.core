<?php

namespace App\Http\Livewire\Ventas;

use App\Core\CQRS\HasCommands;
use Livewire\Component;

class VentasIndex extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;
    
    public function render()
    {
        return view('livewire.ventas.ventas-index')
            ->layout('layouts.app');
    }
}
