<?php

namespace App\Modules\Contabilidad\PeriodoContable\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

use App\Core\CQRS\HasCommands;
use App\Models\Local;
use Illuminate\Support\Facades\Log;

class PeriodoContablePage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public ?int $localId = null;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $periodoContableDeleteId = null;

    #[On('periodo-contable-filtros-actualizados')]
    public function actualizarFiltros(?int $localId = null): void {
        $this->localId = $localId;

        $this->tableVersion++;

        // Log::info('EVENTO PeriodoContable FILTRO ACTUALIZADOS', [
            //             'periodocontableId' => $periodocontableId,
            //         ]);
    }

    #[On('periodo-contable-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener PeriodoContable-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('periodo-contable-editar')]
    public function editarPeriodoContable(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'periodo-contable-cargar-edicion',
            id: $id
        );
    }

    #[On('periodo-contable-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->periodoContableDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->periodoContableDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->periodoContableDeleteId) {
            return;
        }

        $this->command(
            'periodocontable.delete',
            [
                'id' => $this->periodoContableDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Periodo Contable Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->periodoContableDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('periodo-contable-eliminado')]
    public function periodoContableEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        if (!$this->localId) {
            $this->localId = Local::orderBy('nombre')->value('id');
        }

        return view('modules.contabilidad.periodo-contable.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }

}
