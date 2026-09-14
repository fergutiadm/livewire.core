<?php

namespace App\Modules\Comercial\Cliente\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Core\CQRS\HasCommands;

use Illuminate\Support\Facades\Log;

class ClientePage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $clienteDeleteId = null;

    #[On('cliente-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO CLIENTE FILTRO ACTUALIZADOS', [
            //             'clienteId' => $clienteId,
            //         ]);
    }

    #[On('cliente-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener cliente-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('cliente-editar')]
    public function editarCliente(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'cliente-cargar-edicion',
            id: $id
        );
    }

    #[On('cliente-edicion-cargado')]
    public function clienteEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('cliente-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->clienteDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->clienteDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->clienteDeleteId) {
            return;
        }

        $this->command(
            'cliente.delete',
            [
                'id' => $this->clienteDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Cliente Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->clienteDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('cliente-eliminado')]
    public function clienteEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.comercial.cliente.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }

}
