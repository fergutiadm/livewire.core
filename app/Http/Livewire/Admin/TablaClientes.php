<?php

namespace App\Http\Livewire\Admin;

use App\Models\Cliente;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TablaClientes extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';

    public function updatingSearch() { $this->resetPage(); }

    // Soporte para lazy loading
    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando lista de clientes...'
        ]);
    }

    public function edit($id)
    {
        $this->dispatch('edit', id: $id)->to(Clientes::class);
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirmDelete', id: $id)->to(Clientes::class);
    }

    #[On('clientesActualizados')]
    public function recargarClientes()
    {
        usleep(500000);
        $this->resetPage();
    }

    public function render()
    {
        $query = Cliente::query()
            ->where(function($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('telefono', 'like', "%{$this->search}%");
            });


        return view('livewire.admin.tabla-clientes', [
            'clientes' => $query->orderBy('nombre')->paginate($this->perPage)
        ]);
    }
}
