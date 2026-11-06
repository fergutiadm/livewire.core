<?php

namespace App\Modules\Inventario\UnidadMedida\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\UnidadDimension;

class UnidadMedidaPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $unidadMedidaDeleteId = null;

    public ?int $unidadDimensionId = null;


    public function mount(): void
    {
        $this->unidadDimensionId = UnidadDimension::query()
            ->where('activa', true)
            ->orderBy('nombre')
            ->value('id');
    }

    #[On('unidad-medida-filtros-actualizados')]
    public function actualizarFiltros(?int $unidadDimensionId): void {

        $this->unidadDimensionId = $unidadDimensionId;

        $this->dispatch('unidad-medida-tabla-refrescar', filtros: [
            'unidadDimensionId' => $unidadDimensionId,
        ]);
        // ])->to(UnidadMedidaTable::class);
    }

    #[On('unidad-medida-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener unidad-medida-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('unidad-medida-editar')]
    public function editarUnidadMedida(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'unidad-medida-cargar-edicion',
            id: $id
        );
    }

    #[On('unidad-medida-edicion-cargado')]
    public function unidadMedidaEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('unidad-medida-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->unidadMedidaDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->unidadMedidaDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->unidadMedidaDeleteId) {
            return;
        }

        $this->command(
            'unidadMedida.delete',
            [
                'id' => $this->unidadMedidaDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Unidad Dimensión Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->unidadMedidaDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('unidad-medida-eliminado')]
    public function unidadMedidaEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.inventario.unidad-medida.page')
                ->layout('layouts.app_admin');
    }
}