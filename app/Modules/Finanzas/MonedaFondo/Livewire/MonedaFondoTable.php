<?php

namespace App\Modules\Finanzas\MonedaFondo\Livewire;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\Tables\TableComponent;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class MonedaFondoTable extends TableComponent
{
    public ?int $localId = null;
    public ?int $periodoId = null;
    public ?int $conceptoId = null;
    public ?int $monedaId = null;
    public string $fecha = '';

    protected bool $stopLoadingAfterRender = false;

    public function mount(): void
    {
        $this->localId = app(LocalContext::class)->id();
        $this->periodoId = app(PeriodoContext::class)->id();
        $this->fecha  = app(FechaOperacionContext::class)->fecha();
    }

    #[On('moneda-fondo-tabla-refrescar')]
    public function mapearFiltrosYRefrescar(array $filtros): void
    {
        $this->localId = $filtros['localId'] ?? $this->localId;
        $this->periodoId = $filtros['periodoId'] ?? $this->periodoId;

        // Estos filtros forman parte de una fotografía completa.
        // Si llegan como null, deben limpiar el valor anterior.
        $this->conceptoId = $filtros['conceptoId'] ?? null;
        $this->monedaId = $filtros['monedaId'] ?? null;
        $this->fecha = $filtros['fecha'] ?? '';

        $this->stopLoadingAfterRender = true;

        // Log::info('MonedaFondoTable-moneda-fondo-tabla-refrescar', [
        //     '$this->fecha' => $this->fecha,
        //     '$this->monedaId' => $this->monedaId,
        // ]);
    }

    #[On('moneda-fondo-limpiar-filtros')]
    public function limpiarFiltros(): void
    {
        $this->reset(
            'conceptoId',
            'monedaId',
            // 'fecha',
        );
        $this->fecha  = app(FechaOperacionContext::class)->fecha();
    }

    protected function tableClass(): string
    {
        return \App\Modules\Finanzas\MonedaFondo\Tables\MonedaFondoTableDefinition::class;
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

        // Log::info('MonedaFondoTable <-> tableParameters()', [
        //     '$this->fecha' => $this->fecha,
        //     '$this->monedaId' => $this->monedaId,
        // ]);
        return [
            'localId' => $this->localId,
            'periodoId' => $this->periodoId,
            'conceptoId' => $this->conceptoId,
            'monedaId' => $this->monedaId,
            'fecha' => $this->fecha,
        ];
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando fondos por moneda...',
        ]);
    }
}