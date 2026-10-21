<?php

namespace App\Modules\Finanzas\Caja\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class CajaPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $cajaDeleteId = null;

    #[On('caja-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO CAJA FILTRO ACTUALIZADOS', [
            //             'cajaId' => $cajaId,
            //         ]);
    }

    #[On('caja-guardada')]
    public function onSaved(): void
    {
        // Log::info('Listener caja-guardada', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('caja-editar')]
    public function editarCaja(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'caja-cargar-edicion',
            id: $id
        );
    }

    #[On('caja-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->cajaDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->cajaDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->cajaDeleteId) {
            return;
        }

        $this->command(
            'caja.delete',
            [
                'id' => $this->cajaDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Caja Eliminada...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->cajaDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('caja-eliminado')]
    public function cajaEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.caja.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }
}
