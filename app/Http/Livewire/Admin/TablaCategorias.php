<?php

namespace App\Http\Livewire\Admin;

use App\Http\Livewire\Traits\HasReorderableItems;
use App\Models\Categoria;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TablaCategorias extends Component
{
    use WithPagination, HasReorderableItems;

    public $localId;
    public $perPage = 10;
    public $search = '';

    // Este método se ejecuta mientras se carga el componente lazy
    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando lista de categorías...' // Opcional: pasa un mensaje específico
        ]);
    }

    public function edit($id)
    {
        // Emitimos un evento público que el padre capturará
        $this->dispatch('edit', id: $id);
    }

    public function editAtributos($id)
    {
        // Emitimos un evento público que el padre capturará
        $this->dispatch('editAtributos', id: $id);
    }

    public function confirmDelete($id)
    {
        // Emitimos un evento público que el padre capturará
        $this->dispatch('confirmDelete', id: $id);
    }

    #[On('categoriasActualizadas')]
    public function recargarCategorias()
    {
        usleep(500000);
        // Esto fuerza un re-renderizado del componente hijo, refrescando la paginación y la lista
        $this->resetPage();
    }

    /* ================================
       Drag & Drop Categorías
    ================================ */
    public function reorderCategorias(array $orderedIds)
    {
        if (!$this->localId) return;

        // Reordenamiento usando trait
        $this->reorderItems($orderedIds, Categoria::class, fn($query) => $query->where('local_id', $this->localId));

        // Resetear paginación
        $this->resetPage();

        // Evento Livewire 3
        $this->dispatch('categoriasActualizadas');
    }

    public function render()
    {
        $query = Categoria::query()
            ->when($this->localId, fn($q) => $q->where('local_id', $this->localId))
            ->orderBy('orden_visual');

        $categorias = $query->paginate($this->perPage);

        return view('livewire.admin.tabla-categorias', compact('categorias'))
                ->layout('layouts.app_admin');
    }
}
