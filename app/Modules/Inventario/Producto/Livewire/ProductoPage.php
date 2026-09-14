<?php

namespace App\Modules\Inventario\Producto\Livewire;

use App\Models\Local;
use Livewire\Component;
use Livewire\Attributes\On;

use App\Core\CQRS\HasCommands;
use Illuminate\Support\Facades\Log;

class ProductoPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public ?int $localId = null;
    public ?int $categoriaId = null;
    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $productoDeleteId = null;

    public function mount(): void
    {
        // valores iniciales GLOBALS (NO del form)
        $this->localId = Local::orderBy('nombre')->value('id');
    }

    public function syncFilters(?int $localId = null, ?int $categoriaId = null): void
    {
        $this->localId = $localId;
        $this->categoriaId = $categoriaId;

        $this->tableVersion++;
    }


    #[On('producto-filtros-actualizados')]
    public function actualizarFiltros(
        ?int $localId = null,
        ?int $categoriaId = null,
    ): void {
        $this->localId = $localId;

        $this->categoriaId = $categoriaId;

        $this->tableVersion++;

        // Log::info('EVENTO PRODUCTO FILTRO ACTUALIZADOS', [
        //             'localId' => $localId,
        //             'categoriaId' => $categoriaId,
        //             'this->tableVersion' => $this->tableVersion,
        //         ]);
    }

    #[On('producto-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener producto-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('producto-editar')]
    public function editarProducto(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'producto-cargar-edicion',
            id: $id
        );
    }

    #[On('producto-edicion-cargado')]
    public function productoEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('producto-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->productoDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->productoDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->productoDeleteId) {
            return;
        }

        $this->command(
            'producto.delete',
            [
                'id' => $this->productoDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Producto Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->productoDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('producto-eliminado')]
    public function productoEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.inventario.producto.page', [
            'localId' => $this->localId,
            'categoriaId' => $this->categoriaId,
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }

    // public function hydrate(): void
    // {
    //     Log::info('PRODUCTO PAGE - hydrate', [
    //         'time' => microtime(true),
    //     ]);
    // }

    // public function dehydrate(): void
    // {
    //     $inicio = microtime(true);

    //     Log::info('PRODUCTO PAGE - dehydrate INICIO', [
    //         'time' => $inicio,
    //     ]);

    //     Log::info('PRODUCTO PAGE - dehydrate FIN', [
    //         'time' => microtime(true),
    //         'elapsed_ms' => round(
    //             (microtime(true) - $inicio) * 1000,
    //             2
    //         ),
    //     ]);
    // }
}
