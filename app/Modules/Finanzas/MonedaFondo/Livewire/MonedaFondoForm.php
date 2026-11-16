<?php

namespace App\Modules\Finanzas\MonedaFondo\Livewire;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\CQRS\HasCommands;
use App\Models\ConceptoMonedaFondo;
use App\Models\Local;
use App\Models\Moneda;
use App\Models\PeriodoContable;
// use App\Services\FechaPeriodoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class MonedaFondoForm extends Component
{
    use HasCommands;

    public ?int $localId = null;
    public ?int $periodoId = null;
    public ?int $monedaId = null;
    public ?int $conceptoId = null;

    public string $descripcion = '';
    public string $tasaCambio = '1';
    public string $monto = '';
    public string $fecha = '';
    public string $fechaMinima = '';
    public string $fechaMaxima = '';

    // public array $locales = [];
    // public array $periodos = [];
    public array $monedas = [];
    public array $conceptos = [];

    public bool $usar_filtros = false;

    public array $desglose = [];

    protected function rules(): array
    {
        return [
            'localId' => [
                'required',
                'integer',
                'exists:locales,id',
            ],

            'periodoId' => [
                'required',
                'integer',
                'exists:periodos_contables,id',
            ],

            'monedaId' => [
                'required',
                'integer',
                'exists:monedas,id',
            ],

            'conceptoId' => [
                'required',
                'integer',
                'exists:conceptos_moneda_fondo,id',
            ],

            'descripcion' => [
                'required',
                'string',
                'max:255',
            ],

            'tasaCambio' => [
                'required',
                'numeric',
                'gt:0',
                'regex:/^\d{1,10}(?:\.\d{1,10})?$/',
            ],

            'monto' => [
                'required',
                'numeric',
                'gt:0',
                'regex:/^\d{1,18}(?:\.\d{1,2})?$/',
            ],

            'fecha' => [
                'required',
                'date_format:Y-m-d',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'localId.required' =>
                'Seleccione un local.',

            'localId.exists' =>
                'El local seleccionado no existe.',

            'periodoId.required' =>
                'Seleccione un periodo contable.',

            'periodoId.exists' =>
                'El periodo seleccionado no existe.',

            'monedaId.required' =>
                'Seleccione una moneda.',

            'monedaId.exists' =>
                'La moneda seleccionada no existe.',

            'conceptoId.required' =>
                'Seleccione un concepto.',

            'conceptoId.exists' =>
                'El concepto seleccionado no existe.',

            'descripcion.required' =>
                'La descripción es obligatoria.',

            'descripcion.max' =>
                'La descripción no puede superar los 255 caracteres.',

            'tasaCambio.required' =>
                'La tasa de cambio es obligatoria.',

            'tasaCambio.numeric' =>
                'La tasa de cambio debe ser numérica.',

            'tasaCambio.gt' =>
                'La tasa de cambio debe ser mayor que cero.',

            'tasaCambio.regex' =>
                'La tasa admite hasta 10 dígitos enteros y 10 decimales.',

            'monto.required' =>
                'El monto es obligatorio.',

            'monto.numeric' =>
                'El monto debe ser numérico.',

            'monto.gt' =>
                'El monto debe ser mayor que cero.',

            'monto.regex' =>
                'El monto admite hasta 18 dígitos enteros y 2 decimales.',

            'fecha.required' =>
                'La fecha es obligatoria.',

            'fecha.date_format' =>
                'La fecha debe tener el formato YYYY-MM-DD.',
        ];
    }

    public function mount(): void
    {
        $this->localId = app(LocalContext::class)->id();
        $this->periodoId  = app(PeriodoContext::class)->id();
        $this->fecha  = app(FechaOperacionContext::class)->fecha();

        // $this->cargarPeriodos();
        $this->definirRango();
        $this->cargarCatalogos();

        if ($this->fecha === '') {
            $this->fecha = now()->format('Y-m-d');
            $this->definirRango();
        }
    }

    public function abrirCalculadora(): void
    {
        if (!$this->monedaId) {
            $this->addError(
                'monedaId',
                'Seleccione una moneda antes de abrir la calculadora.'
            );

            return;
        }

        $this->resetValidation('monedaId');

        $this->dispatch(
            'calculadora-denominacion-abrir',
            monedaId: (int) $this->monedaId,
            montoEsperado: $this->monto,
            cards : $this->desglose
        );
    }

    #[On('calculadora-denominacion-aplicada')]
    public function aplicarResultadoCalculadora(
        int $monedaId,
        string $total,
        ?array $cards = []
    ): void {
        if ((int) $this->monedaId !== $monedaId) {
            return;
        }

        $this->monto = $total;
        $this->desglose = $cards;

        $this->resetValidation('monto');
    }

    #[On('local-context-changed')]
    public function actualizarLocal(int $localId): void
    {
        $this->localId = $localId;
        // Log::info('MonedaFondoForm-local-context-changed');
    }

    #[On('periodo-context-changed')]
    public function periodoContextChanged(int $periodoId): void
    {
        $this->periodoId = $periodoId;

        $this->definirRango();
        // Log::info('MonedaFondoForm-periodo-context-changed');
    }

    #[On('fecha-operacion-context-changed')]
    public function actualizarFecha(string $fechaOperacion): void
    {
        $this->fecha = \Carbon\Carbon::parse($fechaOperacion)->format('Y-m-d');

        if($this->usar_filtros){
            $this->notificarFiltros();
        }
        // Log::info('MonedaFondoForm-fecha-operacion-context-changed', [
        //     '$this->fecha' => $this->fecha
        // ]);
    }

    private function cargarCatalogos(): void
    {
        // $this->locales = Local::query()
        //     ->operativos()
        //     ->orderBy('nombre')
        //     ->get(['id', 'nombre'])
        //     ->toArray();

        $this->monedas = Moneda::query()
            ->where('activa', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'codigo', 'simbolo', 'tasa_cambio'])
            ->toArray();

        $this->conceptos = ConceptoMonedaFondo::query()
            ->activos()
            ->where('tipo', ConceptoMonedaFondo::TIPO_ESTANDAR)
            ->ordenados()
            ->get([
                'id',
                'nombre',
                'entrada_salida',
            ])
            ->toArray();
    }

    public function updatedFecha(): void
    {
        // if($this->fecha === '')
        // {
        //     $this->fecha  = app(FechaOperacionContext::class)->fecha();
        // }

        $this->notificarFiltros();
        // Log::info('MonedaFondoForm-updatedFecha',[
        //     '$this->fecha' => $this->fecha
        // ]);
    }

    // public function updatedLocalId(): void
    // {
    //     $this->periodoId = null;

    //     $this->cargarPeriodos();

    //     $this->fechaMinima = '';
    //     $this->fechaMaxima = '';

    //     $this->resetValidation([
    //         'localId',
    //         'periodoId',
    //     ]);
    // }

    // private function cargarPeriodos(): void
    // {
    //     if (!$this->localId) {
    //         $this->periodos = [];
    //         return;
    //     }

    //     $this->periodos = PeriodoContable::query()
    //         ->deLocalVigente($this->localId)
    //         // ->where('local_id', $this->localId)
    //         // ->where('cerrado', false)
    //         ->orderByDesc('fecha_inicio')
    //         ->get([
    //             'id',
    //             'nombre',
    //             'fecha_inicio',
    //             'fecha_fin',
    //         ])
    //         ->toArray();
    // }

    // public function updatedPeriodoId(): void
    // {
    //     $this->fechaMinima = '';
    //     $this->fechaMaxima = '';

    //     if($this->periodoId){
    //         $this->fecha = app(FechaPeriodoService::class)->fecha($this->periodoId);
    //         $this->definirRango();
    //     }
    // }

    public function definirRango(): void
    {
        if (!$this->periodoId) {
            $this->fechaMinima = now()->startOfWeek()->format('Y-m-d');
            $this->fechaMaxima = now()->endOfWeek()->format('Y-m-d');

            return;
        }

        try{
            $periodo = PeriodoContable::findOrFail($this->periodoId);
        }catch(ModelNotFoundException $e){
            $this->dispatch('livewire:alert',[
                'message' => 'Periodo no registrado',
                'type' => 'error',
            ]);
            return;
        }

        // $periodo = collect($this->periodos)
        //     ->firstWhere('id', );

        if (!$periodo) {
            $this->fechaMinima = '';
            $this->fechaMaxima = '';

            return;
        }

        $this->fechaMinima = format_fecha($periodo['fecha_inicio'], 'Y-m-d');
        $this->fechaMaxima = format_fecha($periodo['fecha_fin'], 'Y-m-d');
    }

    public function updatedMonedaId(): void
    {
        $moneda = collect($this->monedas)
            ->firstWhere('id', $this->monedaId);
        if($moneda){
            $this->tasaCambio = $moneda['tasa_cambio'];
        }

        $this->resetValidation('monedaId');
        $this->monto = 0;
        $this->desglose = [];

        $this->notificarFiltros();
    }

    public function aplicarFiltrosResultado()
    {
        $this->notificarFiltros();
    }

    private function notificarFiltros(): void
    {
        // if(!$this->usar_filtros){
        //     $this->dispatch('moneda-fondo-limpiar-filtros');
        // }

        $this->dispatch('moneda-fondo-filtros-actualizados',
            localId: $this->localId,
            periodoId: $this->periodoId,
            conceptoId: $this->usar_filtros ? $this->conceptoId : null,
            monedaId: $this->usar_filtros ? $this->monedaId : null,
            fecha: $this->fecha,
            // fecha: $this->usar_filtros ? $this->fecha : null,
        );
    }

    public function save(): void
    {
        try {
            $this->validate();

            $this->command(
                'monedaFondo.create',
                [
                    'local_id' => $this->localId,
                    'periodo_id' => $this->periodoId,
                    'moneda_id' => $this->monedaId,
                    'concepto_id' => $this->conceptoId,
                    'descripcion' => trim($this->descripcion),
                    'monto' => $this->monto,
                    'tasa_cambio' => $this->tasaCambio ?: '1',
                    'desglose' => $this->desglose ,
                    'fecha' => $this->fecha,
                ]
            );

            $this->dispatch(
                'moneda-fondo-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Movimiento financiero registrado correctamente...',
                    'type' => 'success',
                ]
            );

            $this->dispatch(
                'moneda-fondo-guardado'
            );

            $this->resetForm();
        } catch (\Illuminate\Validation\ValidationException $e) {

            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }

            return;
        } finally {
            $this->dispatch('loading-stop');
        }
    }

    public function cancel(): void
    {
        $this->resetForm();

        $this->dispatch(
            'livewire:alert',
            [
                'message' => 'Acción cancelada...',
                'type' => 'warning',
            ]
        );
    }

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            // 'localId',
            // 'periodoId',
            // 'fecha',
            'monedaId',
            'conceptoId',
            'descripcion',
            'monto',
            'desglose',
        ]);

        $this->tasaCambio = '1';
        // $this->fecha = now()->format('Y-m-d');
        $this->fecha  = app(FechaOperacionContext::class)->fecha();

        // $this->periodos = [];

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.finanzas.moneda-fondo.form');
    }
}