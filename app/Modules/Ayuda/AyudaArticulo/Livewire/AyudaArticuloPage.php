<?php

namespace App\Modules\Ayuda\AyudaArticulo\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class AyudaArticuloPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public ?int $categoriaId = null;
    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $ayudaArticuloDeleteId = null;

    public function mount(): void
    {
        // valores iniciales GLOBALS (NO del form)
    }

    public function syncFilters(?int $categoriaId = null): void
    {
        $this->categoriaId = $categoriaId;

        $this->tableVersion++;
    }


    #[On('ayuda-articulo-filtros-actualizados')]
    public function actualizarFiltros(
        ?int $categoriaId = null,
    ): void {

        $this->categoriaId = $categoriaId;

        $this->tableVersion++;

        // Log::info('EVENTO ARTICULO-AYUDA FILTRO ACTUALIZADOS', [
        //             'categoriaId' => $categoriaId,
        //             'this->tableVersion' => $this->tableVersion,
        //         ]);
    }

    #[On('ayuda-articulo-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener ayuda-articulo-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('ayuda-articulo-editar')]
    public function editarAyudaArticulo(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'ayuda-articulo-cargar-edicion',
            id: $id
        );
    }

    #[On('ayuda-articulo-edicion-cargado')]
    public function ayudaArticuloEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    #[On('ayuda-articulo-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->ayudaArticuloDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->ayudaArticuloDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->ayudaArticuloDeleteId) {
            return;
        }

        $this->command(
            'ayudaArticulo.delete',
            [
                'id' => $this->ayudaArticuloDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Articulo de Ayuda Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->ayudaArticuloDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('ayuda-articulo-eliminado')]
    public function ayudaArticuloEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }


    public function render()
    {
        return view('modules.ayuda.ayuda-articulo.page')
            ->layout('layouts.app_admin');
    }
}