<?php

namespace App\Modules\Finanzas\MonedaFondo\Livewire;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\CQRS\HasCommands;
use App\Models\PeriodoContable;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class MonedaFondoPage extends Component
{
    use HasCommands;

    public ?int $localId = null;
    public ?int $periodoId = null;
    public ?int $monedaId = null;
    public ?int $conceptoId = null;

    public string $fecha = '';

    public bool $showForm = true;

    public int $tableVersion = 0;

    public bool $mostrarTasaCambio = false;

    public function mount(SetupConfigService $config): void
    {
        $this->mostrarTasaCambio = $config->get('mostrar_tasa_cambio');
        $this->localId = app(LocalContext::class)->id();
        $this->periodoId  = app(PeriodoContext::class)->id();
        $this->fecha  = app(FechaOperacionContext::class)->fecha();
        $this->evaluarVisibilidadFormulario($this->periodoId);
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

            // Emitimos un solo evento global que actualizará tanto al PeriodoSelector como a esta misma página
            $this->dispatch('periodo-context-changed', periodoId: $periodoContableVigenteId);
        }
        // Log::info('MonedaFondoPage-local-context-changed');
    }

    #[On('periodo-context-changed')]
    public function periodoContextChanged(int $periodoId): void
    {
        if($this->periodoId === $periodoId)
        {
            return;
        }

        $this->periodoId = $periodoId;

        $this->evaluarVisibilidadFormulario($periodoId);
        // Log::info('MonedaFondoPage-periodo-context-changed');
    }

    #[On('fecha-operacion-context-changed')]
    public function actualizarFecha(string $fechaOperacion): void
    {
        $fechaOpFormateada = \Carbon\Carbon::parse($fechaOperacion)->format('Y-m-d');

        if($this->fecha != $fechaOpFormateada)
        {
            $this->fecha = $fechaOpFormateada;
            $this->dispatch('moneda-fondo-filtros-actualizados',
                localId: $this->localId,
                periodoId: $this->periodoId,
                conceptoId: $this->conceptoId,
                monedaId: $this->monedaId,
                fecha: $this->fecha,
                // fecha: $this->usar_filtros ? $this->fecha : null,
            );
        }

        // Log::info('MonedaFondoPage-fecha-operacion-context-changed', [
        //     '$this->fecha' => $this->fecha
        // ]);
    }

    #[On('moneda-fondo-filtros-actualizados')]
    public function actualizarFiltros(
        ?int $localId = null,
        ?int $periodoId = null,
        ?int $conceptoId = null,
        ?int $monedaId = null,
        ?string $fecha = '',
    ): void{
        // $this->tableVersion++;
        $this->localId = $localId;
        $this->periodoId = $periodoId;
        $this->conceptoId = $conceptoId;
        $this->monedaId = $monedaId;
        $this->fecha = $fecha;

        #$this->tableVersion++;

        // Notificamos directamente a GastoTable pasándole los nuevos filtros de golpe
        $this->dispatch('moneda-fondo-tabla-refrescar', filtros: [
            'localId' => $localId,
            'periodoId' => $periodoId,
            'conceptoId' => $conceptoId,
            'monedaId' => $monedaId,
            'fecha' => $fecha,
        ])->to(MonedaFondoTable::class);

        // Log::info('MonedaFondoPage-moneda-fondo-filtros-actualizados dispatch???', [
        //     '$fecha' => $fecha
        // ]);
    }

    #[On('moneda-fondo-guardado')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    public function toggleForm(): void
    {
        $this->showForm = !$this->showForm;
    }

    public function render()
    {
        return view('modules.finanzas.moneda-fondo.page')
            ->layout('layouts.app');
    }
}