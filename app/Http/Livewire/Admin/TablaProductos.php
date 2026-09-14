<?php

namespace App\Http\Livewire\Admin;

use App\Models\Producto;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\Traits\HasReorderableItems;

class TablaProductos extends Component
{
    use WithPagination, HasReorderableItems;

    public $localId;
    public $categoriaId;
    public $perPage = 10;
    public $search = '';

    // Este método se ejecuta mientras se carga el componente lazy
    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando lista de productos...' // Opcional: pasa un mensaje específico
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

    public function editAtributos($id)
    {
        // Emitimos un evento público que el padre capturará
        $this->dispatch('editAtributos', id: $id);
    }

    #[On('productosActualizados')]
    public function recargarProductos()
    {
        usleep(500000); // Retarda el refresco de la tabla medio segundo
        // Esto fuerza un re-renderizado del componente hijo, refrescando la paginación y la lista
        $this->resetPage();
    }

    public function reorderProductos(array $orderedIds)
    {
        $this->reorderItems($orderedIds, Producto::class);

        // Resetear paginación
        $this->resetPage();

        $this->dispatch('productosReordenados');
    }

    public function render()
    {
        $productos = Producto::query()
            ->with('categoria')
            ->when($this->localId, fn($q) => $q->where('local_id', $this->localId))
            ->when($this->categoriaId, fn($q) => $q->where('categoria_id', $this->categoriaId))
            ->paginate($this->perPage);

        return view('livewire.admin.tabla-productos', compact('productos'));
    }
}
