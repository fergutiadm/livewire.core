<?php

namespace App\Modules\Comercial\Gestor\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class GestorPage extends Component
{
    use HasCommands;

    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $gestorDeleteId = null;

    #[On('gestor-filtros-actualizados')]
    public function actualizarFiltros(): void
    {
        $this->tableVersion++;
    }

    #[On('gestor-guardado')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    #[On('gestor-editar')]
    public function editarGestor(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'gestor-cargar-edicion',
            id: $id
        );
    }

    #[On('gestor-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->gestorDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->gestorDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada.',
            'type' => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->gestorDeleteId) {
            return;
        }

        $this->command(
            'gestor.delete',
            [
                'id' => $this->gestorDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Gestor eliminado.',
            'type' => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->gestorDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('gestor-eliminado')]
    public function gestorEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.comercial.gestor.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }
}