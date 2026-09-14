<?php

namespace App\Modules\Inventario\Local\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Core\CQRS\HasCommands;

use Illuminate\Support\Facades\Log;

class LocalPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $localDeleteId = null;

    #[On('local-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO LOCAL FILTRO ACTUALIZADOS', [
            //             'localId' => $localId,
            //         ]);
    }

    #[On('local-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener local-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('local-editar')]
    public function editarLocal(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'local-cargar-edicion',
            id: $id
        );
    }

    #[On('local-edicion-cargado')]
    public function localEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('local-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->localDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->localDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->localDeleteId) {
            return;
        }

        $this->command(
            'local.delete',
            [
                'id' => $this->localDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Local Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->localDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('local-eliminado')]
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
        return view('modules.inventario.local.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }

}