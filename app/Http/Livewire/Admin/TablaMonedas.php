<?php

namespace App\Http\Livewire\Admin;

use App\Models\Moneda;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TablaMonedas extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';

    // Este método se ejecuta mientras se carga el componente lazy
    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando lista de monedas...' // Opcional: pasa un mensaje específico
        ]);
    }

    public function edit($id)
    {
        // Emitimos un evento público que el padre capturará
        $this->dispatch('edit', id: $id);
    }

    public function confirmDelete($id)
    {
        // Emitimos un evento público que el padre capturará
        $this->dispatch('confirmDelete', id: $id);
    }

    #[On('monedasActualizados')]
    public function recargarMonedas()
    {
        usleep(500000); // Retarda el refresco de la tabla medio segundo
        // Esto fuerza un re-renderizado del componente hijo, refrescando la paginación y la lista
        $this->resetPage();
    }

    public function render()
    {
        $monedas = Moneda::orderBy('es_principal', 'desc')->paginate($this->perPage);

        return view('livewire.admin.tabla-monedas', compact('monedas'));
    }
}
