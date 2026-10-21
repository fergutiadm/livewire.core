<?php

namespace App\Modules\Finanzas\CierreCaja\Livewire;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\CQRS\HasCommands;
use App\Models\CierreCaja;
use App\Models\PeriodoContable;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CierreCajaPage extends Component
{
    use HasCommands;
    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $showDeleteModal = false;

    public ?int $cajaDeleteId = null;   

    public function mount(): void
    {
        $periodoContableId = app(PeriodoContext::class)->id();

        $this->evaluarVisibilidadFormulario($periodoContableId);
    }   

    private function evaluarVisibilidadFormulario(?int $periodoActualId): void
    {
        $localId = app(LocalContext::class)->id();

        $periodoVigenteId = PeriodoContable::query()
            ->where('activo', true)
            ->where('cerrado', false)
            ->where('local_id', $localId)
            ->value('id');

        $this->showForm = ($periodoActualId === $periodoVigenteId);
    }

    #[On('periodo-context-changed')]
    public function periodoContextChanged(int $periodoId): void
    {
        $this->evaluarVisibilidadFormulario($periodoId);
    }

    #[On('cierre-caja-filtros-actualizados')]
    public function actualizarFiltros(
        ?int $localId = null,
        ?int $periodoContableId = null,
        ?int $cajaId = null,
        ): void {

        Log::info('actualizarFiltros', [
            'localId' => $localId,
            'periodoContableId' => $periodoContableId,
            'cajaId' => $cajaId,
        ]);

        // $this->tableVersion++;

        $this->dispatch('cierre-caja-tabla-refrescar', filtros: [
            'localId' => $localId,
            'periodoContableId' => $periodoContableId,
            'cajaId' => $cajaId,
        ])->to(CierreCajaTable::class);

        // Log::info('EVENTO CIERRE-CAJA FILTRO ACTUALIZADOS');
    }

    #[On('cierre-caja-guardado')]
    public function onSaved(): void
    {
        // Log::info('Listener cierre-caja-guardada', ['$this->tableVersion'=>$this->tableVersion]);
        $this->tableVersion++;
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR DETALLES
    |--------------------------------------------------------------------------
    */

    #[On('cierre-caja-moneda-detalles-editar')]
    public function editarCierreCajaDetalles(int $id): void
    {
        // Log::info('CierreCajaPage - editarCierreCajaMonedaDetalles');
        $this->dispatch(
            'cierre-caja-moneda-detalles-cargar-edicion',
            id: $id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    #[On('cierre-caja-editar')]
    public function editarCierreCaja(int $id): void
    {
        $this->showForm = true;

        $this->dispatch(
            'cierre-caja-cargar-edicion',
            id: $id
        );
    }

    #[On('cierre-caja-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->cajaDeleteId = $id;
        $this->showDeleteModal = true;

        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->cajaDeleteId = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type'    => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->cajaDeleteId) {
            return;
        }

        $this->command(
            'cierreCaja.delete',
            [
                'id' => $this->cajaDeleteId,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Cierre de Caja Eliminada...',
            'type'    => 'success',
        ]);

        $this->showDeleteModal = false;
        $this->cajaDeleteId = null;

        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('cierre-caja-eliminado')]
    public function cierreCajaEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.cierre-caja.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app');
    }
}
