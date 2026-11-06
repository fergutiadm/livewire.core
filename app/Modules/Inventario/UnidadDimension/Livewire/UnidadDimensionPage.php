<?php

namespace App\Modules\Inventario\UnidadDimension\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class UnidadDimensionPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $unidadDimensionDeleteId = null;

    #[On('unidad-dimension-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO UNIDAD DIMENSION FILTRO ACTUALIZADOS', [
            //             'unidadDimensionId' => $unidadDimensionId,
            //         ]);
    }

    #[On('unidad-dimension-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener unidad-dimension-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('unidad-dimension-editar')]
    public function editarUnidadDimension(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'unidad-dimension-cargar-edicion',
            id: $id
        );
    }

    #[On('unidad-dimension-edicion-cargado')]
    public function unidadDimensionEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('unidad-dimension-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->unidadDimensionDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->unidadDimensionDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->unidadDimensionDeleteId) {
            return;
        }

        $this->command(
            'unidadDimension.delete',
            [
                'id' => $this->unidadDimensionDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Unidad Dimensión Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->unidadDimensionDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('unidad-dimension-eliminado')]
    public function unidadDimensionEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.inventario.unidad-dimension.page')
                ->layout('layouts.app_admin');
    }
}