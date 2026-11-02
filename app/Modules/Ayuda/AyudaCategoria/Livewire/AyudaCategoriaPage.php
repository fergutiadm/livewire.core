<?php

namespace App\Modules\Ayuda\AyudaCategoria\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class AyudaCategoriaPage extends Component
{
    use HasCommands;

    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $categoriaDeleteId = null;

    public function mount(): void
    {
        // Valor inicial GLOBAL de la página.
        // No corresponde al estado interno del formulario.
    }

    #[On('ayuda-categoria-filtros-actualizados')]
    public function actualizarFiltros(
        ?int $categoriaId = null
    ): void {

        $this->tableVersion++;
    }


    #[On('ayuda-categoria-guardada')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    #[On('ayuda-categoria-editar')]
    public function editarCategoria(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'ayuda-categoria-cargar-edicion',
            id: $id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    #[On('ayuda-categoria-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->categoriaDeleteId = $id;

        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;

        $this->categoriaDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type' => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->categoriaDeleteId) {
            return;
        }

        $this->command(
            'ayudaCategoria-.delete',
            [
                'id' => $this->categoriaDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Categoría Eliminada...',
            'type' => 'success',
        ]);

        $this->showDeleteModal = false;

        $this->categoriaDeleteId = null;

        $this->tableVersion++;

        // $this->dispatch('reset-form');
        $this->dispatch('ayuda-ayuda-categoria-eliminada');
    }

    #[On('ayuda-categoria-eliminada')]
    public function categoriaEliminada(): void
    {
        $this->tableVersion++;
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO
    |--------------------------------------------------------------------------
    */

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }
    
    public function render()
    {
        return view('modules.ayuda.ayuda-categoria.page')
            ->layout('layouts.app_admin');
    }
}