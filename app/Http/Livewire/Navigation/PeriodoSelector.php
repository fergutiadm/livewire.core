<?php

namespace App\Http\Livewire\Navigation;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Models\PeriodoContable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class PeriodoSelector extends Component
{
    public bool $showModal = false;

    public ?int $periodoId = null;

    public Collection $periodos;

    public bool $periodoSelectedActivo = false;

    public ?int $year_selector = null;

    public ?int $month_selector = null;

    public array $datosPeriodo = [];

    public function mount(PeriodoContext $periodoContext): void
    {
        $this->periodoId = $periodoContext->id();

        $periodo = $this->buscarPeriodo($this->periodoId);

        if ($periodo) {
            $this->year_selector = (int) $periodo->fecha_inicio->year;
            $this->month_selector = (int) $periodo->fecha_inicio->month;
        } else {
            $this->year_selector = now()->year;
            $this->month_selector = now()->month;
        }

        $this->cargarPeriodos();
    }

    private function consultaPeriodos()
    {
        $localId = app(LocalContext::class)->id();

        return PeriodoContable::query()
            ->when(
                filled($localId),
                fn ($query) => $query->where('local_id', $localId)
            );
    }

    private function buscarPeriodo(?int $periodoId): ?PeriodoContable
    {
        if (!$periodoId) {
            return null;
        }

        return $this->consultaPeriodos()->find($periodoId);
    }

    /**
     * Calcula los años y meses disponibles para los filtros.
     *
     * Un año o mes está disponible si algún período comienza
     * o termina en ese año o mes.
     */
    public function dataPeriodo(): array
    {
        $consulta = $this->consultaPeriodos();

        $periodosFechas = (clone $consulta)
            ->get(['fecha_inicio', 'fecha_fin']);

        $aniosDisponibles = $periodosFechas
            ->flatMap(function ($periodo) {
                return [
                    (int) $periodo->fecha_inicio->year,
                    (int) $periodo->fecha_fin->year,
                ];
            })
            ->unique()
            ->sort()
            ->values();

        $anioReferencia = $this->year_selector
            ?? (int) now()->year;

        // Priorizar el año anterior, el seleccionado y el siguiente.
        $aniosPreferidos = [
            $anioReferencia - 1,
            $anioReferencia,
            $anioReferencia + 1,
        ];

        $anios = $aniosPreferidos
            ? $aniosDisponibles
                ->filter(fn ($anio) => in_array(
                    $anio,
                    $aniosPreferidos,
                    true
                ))
                ->values()
            : collect();

        // Completar hasta tres opciones con los años disponibles
        // más cercanos al año de referencia.
        $restantes = $aniosDisponibles
            ->reject(fn ($anio) => $anios->contains($anio))
            ->sortBy(fn ($anio) => [
                abs($anio - $anioReferencia),
                $anio,
            ])
            ->values();

        $anios = $anios
            ->concat($restantes)
            ->take(3)
            ->sort()
            ->values();

        $anio = $this->year_selector;

        $mesesDisponibles = $periodosFechas
            ->flatMap(function ($periodo) use ($anio) {
                $meses = [];

                if ((int) $periodo->fecha_inicio->year === $anio) {
                    $meses[] = (int) $periodo->fecha_inicio->month;
                }

                if ((int) $periodo->fecha_fin->year === $anio) {
                    $meses[] = (int) $periodo->fecha_fin->month;
                }

                return $meses;
            })
            ->unique()
            ->sort()
            ->values();

        $nombresMeses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $meses = $mesesDisponibles
            ->mapWithKeys(fn ($mes) => [
                $mes => $nombresMeses[$mes],
            ])
            ->all();

        return [
            'year_inicio' => $this->year_selector ?? now()->year,
            'year_fin' => $this->year_selector ?? now()->year,
            'month_inicio' => $this->month_selector ?? now()->month,
            'month_fin' => $this->month_selector ?? now()->month,
            'anios' => $anios->all(),
            'meses' => $meses,
        ];
    }

    /**
     * Recarga los filtros y la lista de períodos.
     */
    private function cargarPeriodos(): void
    {
        $this->datosPeriodo = $this->dataPeriodo();

        $anio = $this->year_selector;
        $mes = $this->month_selector;

        if (!$anio || !$mes) {
            $this->periodos = collect();

            $this->periodoSelectedActivo = false;

            return;
        }

        $this->periodos = $this->consultaPeriodos()
            ->where(function ($query) use ($anio, $mes) {
                $query->where(function ($q) use ($anio, $mes) {
                    $q->whereYear('fecha_inicio', $anio)
                        ->whereMonth('fecha_inicio', $mes);
                })->orWhere(function ($q) use ($anio, $mes) {
                    $q->whereYear('fecha_fin', $anio)
                        ->whereMonth('fecha_fin', $mes);
                });
            })
            ->orderByDesc('activo')
            ->orderByDesc('fecha_inicio')
            ->get();

        $this->periodoSelectedActivo = (bool) (
            $this->periodos
                ->firstWhere('id', $this->periodoId)
                ?->activo ?? false
        );
    }

    public function updatedYearSelector($anio): void
    {
        $this->year_selector = (int) $anio;

        $this->datosPeriodo = $this->dataPeriodo();

        $meses = $this->datosPeriodo['meses'];

        $mesActual = (int) now()->month;

        if (array_key_exists($mesActual, $meses)) {
            $this->month_selector = $mesActual;
        } else {
            $this->month_selector = !empty($meses)
                ? (int) array_key_first($meses)
                : null;
        }

        $this->cargarPeriodos();
    }


    public function updatedMonthSelector($mes): void
    {
        $this->month_selector = filled($mes)
            ? (int) $mes
            : null;

        $this->cargarPeriodos();
    }

    #[On('periodo-context-changed')]
    public function actualizarPeriodoActual(int $periodoId): void
    {
        $periodo = $this->buscarPeriodo($periodoId);

        if (!$periodo) {
            return;
        }

        $this->periodoId = $periodoId;
        $this->year_selector = (int) $periodo->fecha_inicio->year;
        $this->month_selector = (int) $periodo->fecha_inicio->month;

        $this->cargarPeriodos();

        // Log::info('periodo-context-changed', [
        //     'periodoId' => $periodoId,
        //     'year_selector' => $this->year_selector,
        //     'month_selector' => $this->month_selector,
        // ]);

        $this->dispatch('fecha-operacion-seleccion');
    }

    public function seleccionarPeriodo(int $periodoId): void
    {
        // Solo permitir seleccionar períodos que aparecen
        // en la lista filtrada y pertenecen al local actual.
        $periodo = $this->periodos->firstWhere('id', $periodoId);

        if (!$periodo) {
            return;
        }

        app(PeriodoContext::class)->set($periodoId);

        $this->periodoId = $periodoId;
        $this->year_selector = (int) $periodo->fecha_inicio->year;
        $this->month_selector = (int) $periodo->fecha_inicio->month;
        $this->showModal = false;

        $this->cargarPeriodos();

        $this->dispatch(
            'periodo-context-changed',
            periodoId: $periodoId
        );

        $this->dispatch('fecha-operacion-seleccion');
    }

    public function openModal(): void
    {
        $this->cargarPeriodos();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function render()
    {
        $periodoActual = $this->buscarPeriodo($this->periodoId);

        return view('navigation.periodo-selector', [
            'fecha_inicio' => $periodoActual?->fecha_inicio,
            'fecha_fin' => $periodoActual?->fecha_fin,
        ]);
    }
}
