<?php

namespace App\Http\Livewire\Admin;

use App\Models\PeriodoContable;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TablaPeriodosContables extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';

    public $localId;

    // Este método se ejecuta mientras se carga el componente lazy
    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando lista de periodos...' // Opcional: pasa un mensaje específico
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

    #[On('periodosActualizados')]
    public function recargarPeriodos()
    {
        usleep(500000); // Retarda el refresco de la tabla medio segundo
        // Esto fuerza un re-renderizado del componente hijo, refrescando la paginación y la lista
        $this->resetPage();
    }

    public function render()
    {
        $periodos_contables = PeriodoContable::query()
            ->when($this->localId, fn($q) => $q->deLocal($this->localId))
            ->orderByDesc('activo')
            ->orderByDesc('fecha_inicio')
            ->paginate($this->perPage);


        return view('livewire.admin.tabla-periodos-contables', compact('periodos_contables'));
    }
}
