<?php

namespace App\Modules\Gastos\Gasto\Livewire;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\Tables\TableComponent;
use App\Models\Gasto;
use App\Models\PeriodoContable;
use App\Modules\Gastos\Gasto\Actions\ToggleGastoChequeadoAction;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class GastoTable extends TableComponent
{
    public ?int $localId = null;
    public ?int $periodoContableId = null;
    
    public ?int $tipoGastoId = null;
    public ?int $especialistaId = null;
    public ?int $responsableId = null;
    public ?int $operadorId = null;
    public ?int $monedaId = null;

    public string $fecha = '';

    protected bool $stopLoadingAfterRender = false;

    public function mount(): void
    {
        $this->localId = app(LocalContext::class)->id();
        $this->periodoContableId = app(PeriodoContext::class)->id();
    }

    #[On('local-context-changed')]
    public function localContextChanged(int $localId): void
    {
        $this->localId = $localId;
        $this->periodoContableId = PeriodoContable::deLocal($localId)->value('id');
        $this->stopLoadingAfterRender = true;
    }

    #[On('periodo-context-changed')]
    public function periodoContextChanged(int $periodoId): void
    {
        $this->periodoContableId = $periodoId;
        $this->stopLoadingAfterRender = true;
    }
    
    /**
     * OBTENCIÓN ATÓMICA DE FILTROS: Reemplaza a todos los métodos individuales updated() 
     * evitando colas de procesamiento reactivo en Livewire.
     */
    #[On('gasto-tabla-refrescar')]
    public function mapearFiltrosYRefrescar(array $filtros): void
    {
        $this->localId = $filtros['localId'] ?? $this->localId;
        $this->periodoContableId = $filtros['periodoContableId'] ?? $this->periodoContableId;
        $this->tipoGastoId = $filtros['tipoGastoId'] ?? $this->tipoGastoId;
        $this->especialistaId = $filtros['especialistaId'] ?? $this->especialistaId;
        $this->responsableId = $filtros['responsableId'] ?? $this->responsableId;
        $this->operadorId = $filtros['operadorId'] ?? $this->operadorId;
        $this->monedaId = $filtros['monedaId'] ?? $this->monedaId;
        $this->fecha = $filtros['fecha'] ?? $this->fecha;

        $this->stopLoadingAfterRender = true;

        // Log::info('gasto-tabla-refrescar - mapearFiltrosYRefrescar', $filtros);
    }

    #[On('gasto-limpiar-filtros')]
    public function limpiarFiltros(): void
    {
        $this->reset(
            'tipoGastoId',
            'especialistaId',
            'responsableId',
            'operadorId',
            'monedaId',
            'fecha',
        );
    }
    
    protected function tableClass(): string
    {
        return \App\Modules\gastos\Gasto\Tables\GastoTableDefinition::class;
    }

    public function toggleChequeado(int $gastoId): void
    {
        $gasto = Gasto::findOrFail($gastoId);

        app(ToggleGastoChequeadoAction::class)->execute($gasto);
    }

    public function rendered(): void
    {
        if ($this->stopLoadingAfterRender) {
            $this->stopLoadingAfterRender = false;
            $this->dispatch('loading-stop');
        }
    }

    protected function tableParameters(): array
    {
        // Log::info('tableParameters',
        //     [
        //         'localId' => $this->localId,
        //         'periodoContableId' => $this->periodoContableId,
        //         'tipoGastoId' => $this->tipoGastoId,
        //         'especialistaId' => $this->especialistaId,
        //         'responsableId' => $this->responsableId,
        //         'operadorId' => $this->operadorId,
        //         'monedaId' => $this->monedaId,
        //         'fecha' => $this->fecha,
        //     ]
        // );
        return [
            'localId' => $this->localId,
            'tipoGastoId' => $this->tipoGastoId,
            'especialistaId' => $this->especialistaId,
            'responsableId' => $this->responsableId,
            'operadorId' => $this->operadorId,
            'monedaId' => $this->monedaId,
            'periodoContableId' => $this->periodoContableId,
            'fecha' => $this->fecha,
        ];
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando gastos...',
        ]);
    }
}
