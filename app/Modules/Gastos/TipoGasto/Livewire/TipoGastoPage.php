<?php

namespace App\Modules\Gastos\TipoGasto\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;

use Livewire\Component;
use App\Models\Local;

class TipoGastoPage extends Component
{
    use HasCommands;

    public bool $showForm = true;

    public ?int $localId = null;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $tipoGastoDeleteId = null;

    public function mount(): void
    {
        // Valor inicial GLOBAL de la página.
        // No corresponde al estado interno del formulario.
        $this->localId = Local::orderBy('nombre')->value('id');
    }

    public function syncFilters(
        ?int $localId = null,
        ?int $tipoGastoId = null
    ): void {
        $this->localId = $localId;

        $this->tableVersion++;
    }

    #[On('tipo-gasto-filtros-actualizados')]
    public function actualizarFiltros(
        ?int $localId = null,
        ?int $tipoGastoId = null
    ): void {
        $this->localId = $localId;

        $this->tableVersion++;
    }

    #[On('tipo-gasto-guardada')]
    public function onSaved(): void
    {
        // Log::info('Listener tipo-gasto-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('tipo-gasto-editar')]
    public function editartipoGasto(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'tipo-gasto-cargar-edicion',
            id: $id
        );
    }

    #[On('tipo-gasto-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->tipoGastoDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->tipoGastoDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->tipoGastoDeleteId) {
            return;
        }

        $this->command(
            'tipoGasto.delete',
            [
                'id' => $this->tipoGastoDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Tipo de Gasto Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->tipoGastoDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('tipo-gasto-eliminado')]
    public function tipoGastoEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.gastos.tipo-gasto.page', 
        [
            'localId' => $this->localId,
            'tableVersion' => $this->tableVersion,
        ]
        )->layout('layouts.app_admin');
    }
}