<?php

namespace App\Modules\Inventario\TipoLocal\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class TipoLocalPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $tipoLocalDeleteId = null;

    #[On('tipo-local-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO LOCAL FILTRO ACTUALIZADOS', [
            //             'localId' => $tipoLocalId,
            //         ]);
    }

    #[On('tipo-local-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener tipo-local-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('tipo-local-editar')]
    public function editarLocal(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'tipo-local-cargar-edicion',
            id: $id
        );
    }

    #[On('tipo-local-edicion-cargado')]
    public function localEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('tipo-local-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->tipoLocalDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->tipoLocalDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->tipoLocalDeleteId) {
            return;
        }

        $this->command(
            'tipoLocal.delete',
            [
                'id' => $this->tipoLocalDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Local Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->tipoLocalDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('tipo-local-eliminado')]
    public function localEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.inventario.tipo-local.page')
            ->layout('layouts.app_admin');;
    }
}