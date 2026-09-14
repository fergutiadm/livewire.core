<?php

namespace App\Modules\Inventario\Categoria\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Local;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoriaPage extends Component
{
    use HasCommands;

    public bool $showForm = true;

    public ?int $localId = null;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $categoriaDeleteId = null;

    public function mount(): void
    {
        // Valor inicial GLOBAL de la página.
        // No corresponde al estado interno del formulario.
        $this->localId = Local::orderBy('nombre')->value('id');
    }

    public function syncFilters(
        ?int $localId = null,
        ?int $categoriaId = null
    ): void {
        $this->localId = $localId;

        $this->tableVersion++;
    }

    #[On('categoria-filtros-actualizados')]
    public function actualizarFiltros(
        ?int $localId = null,
        ?int $categoriaId = null
    ): void {
        $this->localId = $localId;

        $this->tableVersion++;
    }


    #[On('categoria-guardada')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR ATRIBUTOS
    |--------------------------------------------------------------------------
    */

    #[On('categoria-attributes-editar')]
    public function editarCategoriaAtributes(int $id): void
    {
        $this->dispatch(
            'categoria-attributes-cargar-edicion',
            id: $id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    #[On('categoria-editar')]
    public function editarCategoria(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'categoria-cargar-edicion',
            id: $id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    #[On('categoria-eliminar')]
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
            'categoria.delete',
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
        $this->dispatch('categoria-eliminada');
    }

    #[On('categoria-eliminada')]
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
        return view(
            'modules.inventario.categoria.page',
            [
                'localId' => $this->localId,
                'tableVersion' => $this->tableVersion,
            ]
        )->layout('layouts.app_admin');
    }
}
