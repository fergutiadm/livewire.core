<?php

namespace App\Modules\Gastos\Gasto\Livewire;

use App\Core\Context\FechaOperacionContext;
use Livewire\Component;
use App\Core\CQRS\HasCommands;
use App\Models\Gasto;
use App\Models\TipoGasto;
use App\Models\User;
use App\Models\Moneda;
use Livewire\Attributes\On;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Services\MonedaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GastoForm extends Component
{
    use HasCommands;

    public ?int $gastoId = null;
    public ?int $localId = null;
    public ?int $tipoGastoId = null;
    public ?int $periodoContableId = null;
    public ?int $especialistaId = null;
    public ?int $responsableId = null;
    public ?int $operadorId = null;
    public ?int $monedaId = null;

    public string $cantidad = '';
    public string $valor = '';
    public string $tasa_cambio = '1';
    public string $tasa_cambio_usd = '1';
    public string $fecha = '';
    public string $descripcion = '';

    public ?string $fechaMinima = null;
    public ?string $fechaMaxima = null;

    public $tiposGastos;
    public $usuarios;
    public $monedas;

    public bool $usar_filtros = false;

    protected $rules = [
        'tipoGastoId' => 'required|exists:tipos_gastos,id',
        'especialistaId' => 'required|exists:users,id',
        'responsableId' => 'required|exists:users,id',
        'operadorId' => 'required|exists:users,id',
        'monedaId' => 'required|exists:monedas,id',
        'cantidad' => 'required|numeric|min:1',
        'valor' => 'required|numeric|min:1',
        'descripcion' => 'nullable|string|min:3|max:200',
        'fecha' => 'required|date',
    ];

    protected function messages(): array
    {
        return [
            'tipoGastoId.required' => 'El Tipo de Gasto es obligatorio',
            'especialistaId.required' => 'El Especialista es obligatorio',
            'responsableId.required' => 'El Responsable es obligatorio',
            'operadorId.required' => 'El Operador es obligatorio',
            'monedaId.required' => 'La Moneda es obligatoria',
            'cantidad.required' => 'La Cantidad es obligatoria',
            'cantidad.numeric' => 'La Cantidad debe ser numérica',
            'cantidad.min' => 'La Cantidad tiene que ser mayor que cero',
            'valor.required' => 'El Valor es obligatorio',
            'valor.numeric' => 'El Valor debe ser numérica',
            'valor.min' => 'El Valor tiene que ser mayor que cero',
            'descripcion.min' => 'La Descripción debe tener al menos 3 caracteres',
            'descripcion.max' => 'La Descripción no puede tener más de 200 caracteres',
            'fecha.required' => 'La Fecha es obligatoria.',
        ];
    }

    public function mount(): void
    {
        $this->loadData();
        
        // Asignamos por defecto al operador como el usuario actual al crear
        $this->operadorId = Auth::id();

        $this->dispatch(
            'gasto-filtros-actualizados',
            localId: $this->localId,
            periodoContableId: $this->periodoContableId,
        );
    }

    public function loadData(): void
    {
        $this->localId = app(LocalContext::class)->id();
        $this->periodoContableId = app(PeriodoContext::class)->id();

        $this->fecha = app(FechaOperacionContext::class)->fecha();

        $this->tiposGastos = TipoGasto::orderBy('nombre')->Activos()->get();
        $this->usuarios = User::orderBy('name')->get();
        $this->monedas = Moneda::paraSelect()->get();
    }

    #[On('fecha-operacion-context-changed')]
    public function actualizarFecha(string $fechaOperacion): void
    {
        $this->fecha = $fechaOperacion;
    }

    /**
     * HOOKS AUTOMÁTICOS DE LIVEWIRE: Reemplazan a los antiguos wire:change
     * Se ejecutan solos cuando cambias el select en la vista
     */
    public function updatedResponsableId($value): void
    {
        $this->notificarFiltros();
    }

    public function updatedEspecialistaId($value): void
    {
        $this->notificarFiltros();
    }

    public function updatedTipoGastoId($value): void
    {
        $this->notificarFiltros();
    }

    public function updatedMonedaId($value): void
    {
        $this->notificarFiltros();
    }
    
    public function aplicarFiltrosResultado()
    {
        $this->notificarFiltros();
    }

    private function notificarFiltros(): void
    {
        if(!$this->usar_filtros){
            $this->dispatch('gasto-limpiar-filtros');
        }

        $this->dispatch('gasto-filtros-actualizados',
            localId: $this->localId,
            periodoContableId: $this->periodoContableId,
            tipoGastoId: $this->usar_filtros ? $this->tipoGastoId : null,
            especialistaId: $this->usar_filtros ? $this->especialistaId : null,
            responsableId: $this->usar_filtros ? $this->responsableId : null,
            operadorId: $this->usar_filtros ? $this->operadorId : null,
            monedaId: $this->usar_filtros ? $this->monedaId : null,
            fecha: $this->usar_filtros ? $this->fecha : null,
        );
    }

    public function setAtajoGasto(int $cantidad, float $valor, int $monedaId): void
    {
        $this->cantidad = (string)$cantidad;
        $this->valor = (string)$valor;
        $this->monedaId = $monedaId;
        
        // Ejecuta la lógica existente que creamos para buscar la tasa de cambio de la moneda elegida
        $this->updatedMonedaId($monedaId);
    }


    #[On('gasto-cargar-edicion')]
    public function edit(int $id): void
    {
        // Optimizamos usando el $this->localId interno en vez de resolver del contenedor otra vez
        $gasto = Gasto::where('local_id', $this->localId)->findOrFail($id);

        $this->gastoId = $gasto->id;
        $this->tipoGastoId = $gasto->tipo_gasto_id;
        $this->especialistaId = $gasto->especialista_id;
        $this->responsableId = $gasto->responsable_id;
        $this->operadorId = $gasto->operador_id;
        $this->monedaId = $gasto->moneda_id;
        $this->cantidad = $gasto->cantidad;
        $this->valor = $gasto->valor;
        $this->tasa_cambio = $gasto->tasa_cambio;
        $this->tasa_cambio_usd = $gasto->tasa_cambio_usd;
        $this->fecha = $gasto->fecha;
        $this->descripcion = $gasto->descripcion ?? '';

        $this->dispatch('loading-stop');
        $this->resetValidation();
    }

    #[On('gasto-edicion-cargado')]
    public function gastoEdicionCargado(): void
    {
        // Log::info("EVENTO - gasto-edicion-cargado");
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

    public function save(): void
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('loading-stop');

            throw $e;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        if ($this->gastoId) {

            $gastoActual = Gasto::query()
                ->withExists([
                    'pagosConfirmados',
                ])
                ->findOrFail($this->gastoId);

            if ($gastoActual->pagos_confirmados_exists) {
                $payloadActual = $this->payload();

                $datosEconomicosOriginales = [
                    'moneda_id' => $gastoActual->moneda_id,
                    'cantidad' => $gastoActual->cantidad,
                    'valor' => $gastoActual->valor,
                    'tasa_cambio' => $gastoActual->tasa_cambio,
                    'tasa_cambio_usd' => $gastoActual->tasa_cambio_usd,
                ];

                $datosEconomicosNuevos = [
                    'moneda_id' => $payloadActual['moneda_id'],
                    'cantidad' => $payloadActual['cantidad'],
                    'valor' => $payloadActual['valor'],
                    'tasa_cambio' => $payloadActual['tasa_cambio'],
                    'tasa_cambio_usd' => $payloadActual['tasa_cambio_usd'],
                ];

                if ($datosEconomicosOriginales != $datosEconomicosNuevos) {
                    $this->dispatch('loading-stop');

                    $this->dispatch(
                        'livewire:alert',
                        [
                            'message' =>
                                'No se pueden modificar los datos económicos de un gasto que tiene pagos confirmados.',
                            'type' => 'warning',
                        ]
                    );

                    return;
                }
            }
        

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $gasto = $this->command(
                'gasto.update',
                $payload,
            );

            $this->dispatch(
                'gasto-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Gasto Actualizado correctamente...',
                    'type' => 'success',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE
        |--------------------------------------------------------------------------
        */

        else {

            /*
            |--------------------------------------------------------------------------
            | Actualizando Tasa de cambio
            |--------------------------------------------------------------------------
            */
            $monedaService = app(MonedaService::class);

            $this->tasa_cambio = $monedaService->tasaPorId(
                (int) $this->monedaId
            );

            $this->tasa_cambio_usd = $monedaService->tasaPorCodigo(
                MonedaService::CODIGO_USD
            );

            $gasto = $this->command(
                'gasto.create',
                $this->payload()
            );

            $this->dispatch(
                'gasto-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Gasto Creado correctamente...',
                    'type' => 'success',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Notificar página
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'gasto-guardado'
        );

        $this->dispatch('loading-stop');

        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        $this->resetForm();
    }

    /*
    |--------------------------------------------------------------------------
    | Payload CQRS
    |--------------------------------------------------------------------------
    */

    private function payload(): array
    {
        return [
            'id' => $this->gastoId,

            'local_id' => app(LocalContext::class)->id(),

            'tipo_gasto_id' => $this->tipoGastoId,

            'periodo_contable_id' => $this->periodoContableId,

            'especialista_id' => $this->especialistaId,

            'responsable_id' => $this->responsableId,

            'operador_id' => $this->operadorId,

            'moneda_id' => $this->monedaId,

            'cantidad' => $this->cantidad,
            
            'valor' => $this->valor,
            
            'fecha' => $this->fecha,
            
            'tasa_cambio' => $this->tasa_cambio,
            
            'tasa_cambio_usd' => $this->tasa_cambio_usd,

            'descripcion' => $this->descripcion,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Cancelar
    |--------------------------------------------------------------------------
    */

    public function cancel(): void
    {
        $this->resetForm();

        $this->dispatch(
            'livewire:alert',
            [
                'message' =>
                    'Acción cancelada...',
                'type' =>
                    'warning',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset formulario
    |--------------------------------------------------------------------------
    */

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'gastoId',
            'tipoGastoId',
            'especialistaId',
            'responsableId',
            'operadorId',
            'monedaId',
            'cantidad',
            'valor',
            'descripcion',
        ]);

        $this->fecha = now()->format('Y-m-d');

        $this->tasa_cambio = 1;

        $this->tasa_cambio_usd = 1;

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.gastos.gasto.form');
    }
}
