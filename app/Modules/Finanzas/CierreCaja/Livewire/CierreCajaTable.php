<?php

namespace App\Modules\Finanzas\CierreCaja\Livewire;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\Tables\TableComponent;
use App\Models\PeriodoContable;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class CierreCajaTable extends TableComponent
{
    public ?int $localId = null;
    public ?int $periodoContableId = null;
    public ?int $cajaId = null;
    public ?int $periodoId = null;
    public ?int $operadorId = null;

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
    #[On('cierre-caja-tabla-refrescar')]
    public function mapearFiltrosYRefrescar(array $filtros): void
    {
        $this->localId = $filtros['localId'] ?? $this->localId;
        $this->periodoContableId = $filtros['periodoContableId'] ?? $this->periodoContableId;
        $this->cajaId = $filtros['cajaId'] ?? $this->cajaId;

        $this->stopLoadingAfterRender = true;

        // Log::info('gasto-tabla-refrescar - mapearFiltrosYRefrescar', $filtros);
    }

    protected function tableClass(): string
    {
        return \App\Modules\Finanzas\CierreCaja\Tables\CierreCajaTableDefinition::class;
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
        //         'cajaId' => $this->cajaId,
        //     ]
        // );
        return [
            'localId' => $this->localId,
            'periodoContableId' => $this->periodoContableId,
            'cajaId' => $this->cajaId,
        ];
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando cierres de caja...',
        ]);
    }
}
