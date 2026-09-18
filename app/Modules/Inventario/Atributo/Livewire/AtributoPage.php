<?php

namespace App\Modules\Inventario\Atributo\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class AtributoPage extends Component
{
    use HasCommands;

    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $atributoDeleteId = null;

    #[On('atributo-filtros-actualizados')]
    public function actualizarFiltros(): void
    {
        $this->tableVersion++;
    }

    #[On('atributo-guardado')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    #[On('atributo-editar')]
    public function editarAtributo(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'atributo-cargar-edicion',
            id: $id
        );
    }

    #[On('atributo-edicion-cargado')]
    public function atributoEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('atributo-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->atributoDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->atributoDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type' => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->atributoDeleteId) {
            return;
        }

        $this->command(
            'atributo.delete',
            [
                'id' => $this->atributoDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Atributo desactivado correctamente...',
            'type' => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->atributoDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
        $this->dispatch('loading-stop');
    }

    #[On('atributo-eliminado')]
    public function atributoEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;

        if ($this->showForm) {
            $this->dispatch('atributo-nuevo');
        }
    }

    public function render()
    {
        return view(
            'modules.inventario.atributo.page',
            [
                'tableVersion' => $this->tableVersion,
            ]
        )->layout('layouts.app_admin');
    }
}