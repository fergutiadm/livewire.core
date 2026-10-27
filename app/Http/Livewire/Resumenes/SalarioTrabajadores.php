<?php

namespace App\Http\Livewire\Resumenes;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Models\PeriodoContable;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class SalarioTrabajadores extends Component
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

    private function trabajadoresDemo(): array
    {
        return [
            [
                'id' => 1,
                'nombre' => 'Armando Perez',
                'salario_diario' => 1200,
                'dias_trabajo' => [1, 2, 3, 4, 5, 6],
            ],
            [
                'id' => 2,
                'nombre' => 'Martha Ruiz',
                'salario_diario' => 1500,
                'dias_trabajo' => [1, 2, 3, 4, 5],
            ],
            [
                'id' => 3,
                'nombre' => 'Fernando Jose',
                'salario_diario' => 1000,
                'dias_trabajo' => [1, 3, 5, 6],
            ],
            [
                'id' => 4,
                'nombre' => 'Ramon Gonzalez',
                'salario_diario' => 1300,
                'dias_trabajo' => [1, 2, 3, 4, 5, 6],
            ],
            [
                'id' => 5,
                'nombre' => 'Ana Martinez',
                'salario_diario' => 1100,
                'dias_trabajo' => [1, 2, 4, 5],
            ],
            [
                'id' => 6,
                'nombre' => 'Bertha Ramirez',
                'salario_diario' => 1400,
                'dias_trabajo' => [2, 3, 4, 5, 6],
            ],
            [
                'id' => 7,
                'nombre' => 'Edgar Fernandez',
                'salario_diario' => 900,
                'dias_trabajo' => [1, 3, 5],
            ],
            [
                'id' => 8,
                'nombre' => 'Lucia Marin',
                'salario_diario' => 1250,
                'dias_trabajo' => [1, 2, 3, 4, 5],
            ],
            [
                'id' => 9,
                'nombre' => 'Carlos Diaz',
                'salario_diario' => 1000,
                'dias_trabajo' => [2, 4, 6],
            ],
            [
                'id' => 10,
                'nombre' => 'Elena Suarez',
                'salario_diario' => 1600,
                'dias_trabajo' => [1, 2, 3, 4, 5, 6],
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

        foreach ($this->trabajadoresDemo() as $trabajador) {
            $salarioAcumulado = 0;
            $ventasAcumuladas = 0;
            $dia = $inicio->copy();

            while ($dia->lte($fecha)) {
                if (in_array($dia->dayOfWeekIso, $trabajador['dias_trabajo'], true)) {
                    $salarioAcumulado += $trabajador['salario_diario'];

                    // Importe ficticio para conservar la demostración visual.
                    $ventasAcumuladas += $trabajador['salario_diario'] * 8;
                }

                $dia->addDay();
            }

            $trabajoHoy = in_array(
                $fecha->dayOfWeekIso,
                $trabajador['dias_trabajo'],
                true
            );

            $grupo[] = [
                'id' => $trabajador['id'],
                'nombre' => $trabajador['nombre'],
                'localId' => (string) $this->localId,
                'periodoContableId' => (string) $periodo->id,
                'fecha' => $fecha->format('d/m/Y'),
                'fecha_iso' => $fecha->format('Y-m-d'),
                'ventas' => $trabajoHoy ? $trabajador['salario_diario'] * 8 : 0,
                'salario' => $trabajoHoy ? $trabajador['salario_diario'] : 0,
                'ventas_acumuladas' => $ventasAcumuladas,
                'salario_acumulado' => $salarioAcumulado,
                'trabajo_hoy' => $trabajoHoy,
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

        return view('resumenes.salario-trabajadores', [
            'periodoSeleccionado' => $periodo,
            'grupoTrabajadores' => $this->generarDatosDemo($periodo),
        ]);
    }
}