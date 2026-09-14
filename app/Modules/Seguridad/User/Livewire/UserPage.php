<?php

namespace App\Modules\Seguridad\User\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Core\CQRS\HasCommands;

use Illuminate\Support\Facades\Log;

class UserPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $userDeleteId = null;

    #[On('user-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO USER FILTRO ACTUALIZADOS', [
            //             'userId' => $userId,
            //         ]);
    }

    #[On('user-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener user-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('user-editar')]
    public function editarUser(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'user-cargar-edicion',
            id: $id
        );
    }

    #[On('user-edicion-cargado')]
    public function userEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('user-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->userDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->userDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->userDeleteId) {
            return;
        }

        $this->command(
            'user.delete',
            [
                'id' => $this->userDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'User Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->userDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('user-eliminado')]
    public function userEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.seguridad.user.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }

}
