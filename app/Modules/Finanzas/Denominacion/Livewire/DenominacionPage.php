<?php

namespace App\Modules\Finanzas\Denominacion\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Moneda;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class DenominacionPage extends Component
{
    use HasCommands;

    public bool $showForm = true;

    public ?int $monedaId = null;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $denominacionDeleteId = null;

    public function mount(){
        $this->monedaId = Moneda::paraSelect()->value('id');
    }

    #[On('denominacion-filtros-actualizados')]
    public function actualizarFiltros(?int $monedaId = null): void {

        $this->monedaId = $monedaId;
        $this->tableVersion++;

        /*Log::info('EVENTO DENOMINACION FILTRO ACTUALIZADOS', [
                         'monedaId' => $monedaId,
                     ]);*/
    }

    #[On('denominacion-guardada')]
    public function onSaved(): void
    {
        // Log::info('Listener denominacion-guardado', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    #[On('denominacion-editar')]
    public function editarDenominacion(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'denominacion-cargar-edicion',
            id: $id
        );
    }

    #[On('denominacion-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->denominacionDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->denominacionDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->denominacionDeleteId) {
            return;
        }

        $this->command(
            'denominacion.delete',
            [
                'id' => $this->denominacionDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Denominacion Eliminado...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->denominacionDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('denominacion-eliminado')]
    public function denominacionEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.admin.denominacion.page')
            ->layout('layouts.app_admin');
    }
}