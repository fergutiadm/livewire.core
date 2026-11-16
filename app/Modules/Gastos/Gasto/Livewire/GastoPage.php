<?php

namespace App\Modules\Gastos\Gasto\Livewire;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\CQRS\HasCommands;
use App\Models\PeriodoContable;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Modules\Gastos\Gasto\Livewire\GastoTable;

class GastoPage extends Component
{
    use HasCommands;

    public bool $showForm = true;
    public ?int $localId = null;
    public ?int $periodoContableId = null;
    public ?int $tipoGastoId = null;
    public ?int $especialistaId = null;
    public ?int $responsableId = null;
    public ?int $operadorId = null;
    public ?int $monedaId = null;
    public ?string $fecha = '';
    public int $tableVersion = 0;
    public bool $showDeleteModal = false;
    public ?int $gastoDeleteId = null;
    public bool $mostrarTasaCambio = false;

    public function mount(SetupConfigService $config): void
    {
        $this->mostrarTasaCambio = $config->get('mostrar_tasa_cambio');
        $this->localId = app(LocalContext::class)->id();
        $this->periodoContableId  = app(PeriodoContext::class)->id();
        $this->fecha  = app(FechaOperacionContext::class)->fecha();
        $this->evaluarVisibilidadFormulario($this->periodoContableId);
    }

    private function evaluarVisibilidadFormulario(?int $periodoActualId): void
    {
        $periodoVigenteId = PeriodoContable::query()
            ->where('activo', true)
            ->where('cerrado', false)
            ->where('local_id', $this->localId)
            ->value('id');

        $this->showForm = ($periodoActualId === $periodoVigenteId);
    }

    #[On('local-context-changed')]
    public function localContextChanged(int $localId): void
    {
        Log::info(
            'GastoPage <local-context-changed> localContextChanged',
            [
                'localId' => $localId,
                '$this->localId' => $this->localId,
                'periodoContableId' => $this->periodoContableId,
                'fecha' => $this->fecha,
            ]
        );
        if($this->localId === $localId)
        {
            return;
        }

        $this->localId = $localId;

        $periodoContableVigenteId = PeriodoContable::query()
            ->where('activo', true)
            ->where('cerrado', false)
            ->where('local_id', $localId)
            ->value('id');

        if ($periodoContableVigenteId) {
            app(PeriodoContext::class)->set($periodoContableVigenteId);

            Log::info('GastoPage <-> local-context-changed', [
                '$this->localId' => $this->localId
            ]);

            // Emitimos un solo evento global que actualizará tanto al PeriodoSelector como a esta misma página
            $this->dispatch('periodo-context-changed', periodoId: $periodoContableVigenteId);
        }
    }

    #[On('periodo-context-changed')]
    public function periodoContextChanged(int $periodoId): void
    {
        Log::info(
            'GastoPage <periodo-context-changed> periodoContextChanged',
            [
                'localId' => $this->localId,
                'periodoContableId' => $this->periodoContableId,
                'fecha' => $this->fecha,
            ]
        );
        if($this->periodoContableId === $periodoId)
        {
            return;
        }

        $this->periodoContableId = $periodoId;

        Log::info('GastoPage <-> periodo-context-changed', [
            '$this->periodoContableId' => $this->periodoContableId
        ]);

        $this->evaluarVisibilidadFormulario($periodoId);
    }

    #[On('fecha-operacion-context-changed')]
    public function actualizarFecha(string $fechaOperacion): void
    {
        Log::info(
            'GastoPage <fecha-operacion-context-changed> actualizarFecha',
            [
                'localId' => $this->localId,
                'periodoContableId' => $this->periodoContableId,
                'fecha' => $this->fecha,
            ]
        );

        $fechaOpFormateada = \Carbon\Carbon::parse($fechaOperacion)->format('Y-m-d');

        if($this->fecha != $fechaOpFormateada)
        {
            $this->fecha = $fechaOpFormateada;
            $this->dispatch('gasto-filtros-actualizados',
                localId: $this->localId,
                periodoContableId: $this->periodoContableId,
                fecha: $this->fecha,
                // fecha: $this->usar_filtros ? $this->fecha : null,
            );
        }

        Log::info('GastoPage <-> fecha-operacion-context-changed', [
            '$this->fecha' => $this->fecha
        ]);

    }

    #[On('gasto-filtros-actualizados')]
    public function actualizarFiltros(
        ?int $localId = null,
        ?int $periodoContableId = null,
        ?int $tipoGastoId = null,
        ?int $especialistaId = null,
        ?int $responsableId = null,
        ?int $operadorId = null,
        ?int $monedaId = null,
        ?string $fecha = '',
    ): void {
        $this->localId = $localId;
        $this->periodoContableId = $periodoContableId;
        $this->tipoGastoId = $tipoGastoId;
        $this->especialistaId = $especialistaId;
        $this->responsableId = $responsableId;
        $this->operadorId = $operadorId;
        $this->monedaId = $monedaId;
        $this->fecha = $fecha;

        #$this->tableVersion++;

        // Notificamos directamente a GastoTable pasándole los nuevos filtros de golpe
        $this->dispatch('gasto-tabla-refrescar', filtros: [
            'localId' => $localId,
            'periodoContableId' => $periodoContableId,
            'tipoGastoId' => $tipoGastoId,
            'especialistaId' => $especialistaId,
            'responsableId' => $responsableId,
            'operadorId' => $operadorId,
            'monedaId' => $monedaId,
            'fecha' => $fecha,
        ])->to(GastoTable::class);
    }

    #[On('gasto-guardado')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    #[On('gasto-editar')]
    public function editarGasto(int $id): void
    {
        $this->showForm = true;
        $this->dispatch('gasto-cargar-edicion', id: $id);
    }

    #[On('gasto-eliminar')]
    public function solicitarEliminacion(int $id): void
    {
        $this->gastoDeleteId = $id;
        $this->showDeleteModal = true;
        $this->dispatch('loading-stop');
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteModal = false;
        $this->gastoDeleteId = null;
        $this->dispatch('livewire:alert', ['message' => 'Acción cancelada...', 'type' => 'warning']);
    }

    public function confirmarEliminacion(): void
    {
        if (!$this->gastoDeleteId) return;

        $this->command('gasto.delete', ['id' => $this->gastoDeleteId]);

        $this->dispatch('livewire:alert', ['message' => 'Gasto Eliminado...', 'type' => 'success']);

        $this->showDeleteModal = false;
        $this->gastoDeleteId = null;
        $this->tableVersion++;

        $this->dispatch('reset-form');
    }

    #[On('gasto-eliminado')]
    public function gastoEliminado(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.gastos.gasto.page')->layout('layouts.app');
    }
}
