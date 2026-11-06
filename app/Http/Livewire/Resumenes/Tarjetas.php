<?php

namespace App\Http\Livewire\Resumenes;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Models\PeriodoContable;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class Tarjetas extends Component
{
    public bool $showModal = false;

    public ?int $localId = null;
    public ?int $periodoContableId = null;
    public string $fechaOperacion = '';

    public function mount(
        LocalContext $localContext,
        PeriodoContext $periodoContext,
        FechaOperacionContext $fechaOperacionContext
    ): void {
        $this->localId = $localContext->id();
        $this->periodoContableId = $periodoContext->id();
        $this->fechaOperacion = $fechaOperacionContext->fecha();
    }

    #[On('local-context-changed')]
    public function actualizarLocal(?int $localId = null): void
    {
        $this->localId = $localId ?? app(LocalContext::class)->id();
    }

    #[On('periodo-context-changed')]
    public function actualizarPeriodo(int $periodoId): void
    {
        $this->periodoContableId = $periodoId;
        $this->showModal = false;
    }

    #[On('fecha-operacion-context-changed')]
    public function actualizarFecha(string $fechaOperacion): void
    {
        $this->fechaOperacion = $fechaOperacion;
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function definirParametros(
        ?int $localId = null,
        ?int $periodoContableId = null,
        string $fechaOperacion = ''
    ): void {
        $this->localId = $localId ?? $this->localId;
        $this->periodoContableId = $periodoContableId ?? $this->periodoContableId;

        if ($fechaOperacion !== '') {
            $this->fechaOperacion = $fechaOperacion;
        }

        $this->showModal = false;
    }

    private function tarjetasDemo(): array
    {
        return [
            [
                'id' => 1,
                'nombre' => 'Yunger Azcuy Torrens',
                'moneda' => 'CUP',
                'numero' => '9227-9598-7945-1311',
                'monto' => 12000,
                'dias_venta' => [1, 2, 3, 4, 5, 6],
            ],
            [
                'id' => 2,
                'nombre' => 'Anais Torrens Perez',
                'monto' => 20500,
                'moneda' => 'CUP',
                'numero' => '9206-9598-7327-9780',
                'dias_venta' => [1, 2, 3, 4, 5],
            ],
            [
                'id' => 3,
                'nombre' => 'Anyelaine Mena Azcuy',
                'monto' => 3000,
                'moneda' => 'CLASICA',
                'numero' => '9227-9598-7811-3351',
                'dias_venta' => [1, 3, 5, 6],
            ],
            [
                'id' => 4,
                'nombre' => 'Estefany Azcuy',
                'monto' => 850,
                'moneda' => 'USD',
                'numero' => '9205-9598-7974-0786',
                'dias_venta' => [1, 2, 3, 4, 5, 6],
            ],
        ];
    }

    private function generarDatosDemo(?PeriodoContable $periodo): array
    {
        if (
            !$periodo ||
            !$this->fechaOperacion ||
            !$this->localId ||
            $periodo->local_id != $this->localId
        ) {
            return [];
        }

        $inicio = Carbon::parse($periodo->fecha_inicio)->startOfDay();
        $fin = Carbon::parse($periodo->fecha_fin)->startOfDay();
        $fecha = Carbon::parse($this->fechaOperacion)->startOfDay();

        if ($fecha->lt($inicio) || $fecha->gt($fin)) {
            return [];
        }

        $grupo = [];

        foreach ($this->tarjetasDemo() as $tarjeta) {

            $montoAcumulado = 0;
            $montoTotal = 0;
            $dia = $inicio->copy();

            while ($dia->lte($fecha)) {
                if (in_array($dia->dayOfWeekIso, $tarjeta['dias_venta'], true)) {
                    $montoAcumulado += $tarjeta['monto'] * 8;
                    $montoTotal += $tarjeta['monto'] * 10;
                }

                $dia->addDay();
            }

            $tarjetaHoy = in_array(
                $fecha->dayOfWeekIso,
                $tarjeta['dias_venta'],
                true
            );

            $grupo[] = [
                'id' => $tarjeta['id'],
                'nombre' => $tarjeta['nombre'],
                'moneda' => $tarjeta['moneda'],
                'numero' => $tarjeta['numero'],
                'localId' => (string) $this->localId,
                'periodoContableId' => (string) $periodo->id,
                'fecha' => $fecha->format('d/m/Y'),
                'fecha_iso' => $fecha->format('Y-m-d'),
                'monto' => $tarjetaHoy ? $tarjeta['monto'] : 0,
                'monto_acumulado' => $montoAcumulado,
                'monto_total' => $montoTotal,
                'trabajo_hoy' => $tarjetaHoy,
            ];
        }

        return $grupo;
    }

    public function render()
    {
        $periodo = $this->periodoContableId
            ? PeriodoContable::query()
                ->whereKey($this->periodoContableId)
                ->where('local_id', $this->localId)
                ->first()
            : null;

        return view('resumenes.tarjetas', [
            'periodoSeleccionado' => $periodo,
            'grupoTarjetas' => $this->generarDatosDemo($periodo),
        ]);
    }
}