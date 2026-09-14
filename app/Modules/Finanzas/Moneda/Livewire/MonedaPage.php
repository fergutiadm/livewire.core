<?php

namespace App\Modules\Finanzas\Moneda\Livewire;

use App\Core\CQRS\HasCommands;
use Livewire\Attributes\On;
use Livewire\Component;

class MonedaPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $monedaDeleteId = null;

    #[On('moneda-filtros-actualizados')]
    public function actualizarFiltros(): void {

        $this->tableVersion++;

        // Log::info('EVENTO MONEDA FILTRO ACTUALIZADOS', [
            //             'monedaId' => $monedaId,
            //         ]);
    }

    #[On('moneda-guardada')]
    public function onSaved(): void
    {
        // Log::info('Listener moneda-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('moneda-editar')]
    public function editarMoneda(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'moneda-cargar-edicion',
            id: $id
        );
    }

    #[On('moneda-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->monedaDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->monedaDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->monedaDeleteId) {
            return;
        }

        $this->command(
            'moneda.delete',
            [
                'id' => $this->monedaDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Moneda Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->monedaDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('moneda-eliminado')]
    public function monedaEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.moneda.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }
}
