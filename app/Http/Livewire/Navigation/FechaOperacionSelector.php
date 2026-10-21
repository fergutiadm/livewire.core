<?php

namespace App\Http\Livewire\Navigation;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\Context\FechaOperacionContext;
use App\Models\PeriodoContable;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class FechaOperacionSelector extends Component
{
    public bool $showModal = false;
    public ?int $periodoId = null;
    public bool $periodoSelectedActivo = false;
    public ?PeriodoContable $periodoActual = null;

    public ?string $fechaOperacion = null;
    public array $fechas_periodo = [];

    public function mount(PeriodoContext $periodoContext): void
    {
        $this->periodoId = $periodoContext->id();

        $this->actualizarFechaOperacion($this->periodoId);
        
        $this->cargarFechas();
    }

    /**
     * ESCUCHA EL CAMBIO GLOBAL: Este listener reacciona cuando GastoPage 
     * o el listener de Login modifican el periodo en el servidor.
     */
    #[On('periodo-context-changed')]
    public function actualizarFechaOperacion(int $periodoId): void
    {
        // 1. Buscamos el periodo contable actual en la base de datos
        $periodoActual = PeriodoContable::find($periodoId);

        $hoy = now()->format('Y-m-d');
        if ($periodoActual) {
            // 2. Asignamos las fechas límite (soportando los nombres de columna que tengas)
            $fechaMinima = $periodoActual->fecha_inicio ?? $periodoActual->fecha_inicial ?? null;
            $fechaMaxima = $periodoActual->fecha_fin ?? $periodoActual->fecha_final ?? null;
        }

        $fechaPeriodo = ($hoy < $fechaMinima) ? $fechaMinima : (($hoy > $fechaMaxima) ? $fechaMaxima : $hoy);

        app(FechaOperacionContext::class)->set($fechaPeriodo);
        $this->fechaOperacion = $fechaPeriodo;

    }

    /**
     * OPTIMIZACIÓN EXTRA: Aislamos la carga de periodos para no repetir código
     */
    private function cargarFechas(): void
    {
        $periodoId = app(PeriodoContext::class)->id();
        
        $this->periodoActual = PeriodoContable::findOrFail($periodoId);

        $this->periodoSelectedActivo = $this->periodoActual?->activo ?? false;

        $fecha = $this->periodoActual->fecha_inicio;
        // $fecha = $this->periodoActual->fecha_inicio 
        //     ? \Carbon\Carbon::parse($this->periodoActual->fecha_inicio)->format('Y-m-d')
        //     : '';
        $item_rango_fecha = 1;
        while($fecha <= $this->periodoActual->fecha_fin)
        {
            $this->fechas_periodo[$item_rango_fecha++] = [
                            'es' => \Carbon\Carbon::parse($fecha)->format('d-m-Y'),
                            'en' => \Carbon\Carbon::parse($fecha)->format('Y-m-d'),
                        ];
            $fecha->addDay();
        }

        Log::info('cargarFechas', 
            [
                '$periodoId' => $periodoId,
                'fechas_periodo' => $this->fechas_periodo
            ]
        );
    }

    /**
     * ESCUCHA EL CAMBIO GLOBAL: Este listener reacciona cuando GastoPage 
     * o el listener de Login modifican el periodo en el servidor.
     */
    #[On('fecha-operacion-context-changed')]
    public function actualizarFechaOperacionActual(string $fechaOperacion): void
    {
        // 1. Volvemos a cargar las fechas porque el periodo y/o local pudo haber cambiado
        $this->cargarFechas();
        
        // 2. Sincronizamos el fechaOperacion para que la vista HTML marque la correcto
        $this->fechaOperacion = $fechaOperacion;
        
        Log::info('actualizarFechaOperacionActual',
            [
                '$this->fechaOperacion' => $this->fechaOperacion
            ]
        );
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    #[On('fecha-operacion-definir')]
    public function definirFechaOperacion(?string $fechaOperacion = null): void
    {
        $this->cargarFechas();
        $this->seleccionarFechaOperacion($fechaOperacion);
    }

    public function seleccionarFechaOperacion(?string $fechaOperacion = null): void
    {
        $hoy = now()->format('Y-m-d');

        if(!$fechaOperacion)
            $fechaOperacion = $hoy;

        //Validamos $fechaOperacion

        $fechaOperacion = \Carbon\Carbon::parse($fechaOperacion)->format('Y-m-d');

        if (!$this->periodoActual) {
            return;
        }

        
        if($fechaOperacion < $this->fechas_periodo[1]['en'])
            $fechaOperacion = $this->fechas_periodo[1]['en'];
        
        $last_item = count($this->fechas_periodo);
        if($fechaOperacion > $this->fechas_periodo[$last_item]['en'])
            $fechaOperacion = $this->fechas_periodo[$last_item]['en'];
        
        app(FechaOperacionContext::class)->set($fechaOperacion);
        $this->fechaOperacion = $fechaOperacion;
        $this->showModal = false;
        
        // Notificamos al resto de la aplicación (como a GastoPage)
        $this->dispatch(
            'fecha-operacion-context-changed',
            fechaOperacion: $fechaOperacion,
        );
    }

    public function render()
    {
        return view('navigation.fecha-operacion-selector', [
            'fecha_inicio' => $this->periodoActual->fecha_inicio,
            'fecha_fin' => $this->periodoActual->fecha_fin,
        ]);
    }
}
