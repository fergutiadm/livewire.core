<?php

namespace App\Modules\Finanzas\CierreCaja\Livewire;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Core\CQRS\HasCommands;
use App\Models\Caja;
use App\Models\CierreCaja;
use App\Models\Moneda;
use App\Models\PeriodoContable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CierreCajaForm extends Component
{
    use HasCommands;

    public ?int $cierreCajaId = null;
    
    public ?int $localId = null;
    
    public ?int $cajaId = null;
    
    public ?int $periodoContableId = null;
    
    public ?int $operadorId = null;
    
    public string $fecha = '';
    
    public ?int $monedaId = null;
    public string $monto_esperado_edit = '';
    public string $monto_contado_edit = '';

    public string $estado = '';

    public array $cierreMonedas = [];

    public array $monedas = [];

    public ?int $monedaEditandoId = null;

    public int $formCierreMonedaVersion = 0;

    public bool $usar_filtros = false;

    protected $rules = [
        'cajaId' => 'required|exists:cajas,id',
        'fecha' => 'required|date',
        ];

    protected function messages(): array
    {
        return [
            'cajaId.required' =>
                'El Caja es obligatorio',
            'cajaId.exists' =>
                'El Caja seleccionado no es válido',

            'fecha.required' => 'La Fecha es obligatoria.',
        ];

    }

    public function mount(): void
    {
        $this->localId = app(LocalContext::class)->id();
        $this->periodoContableId = app(PeriodoContext::class)->id();
        $this->operadorId = Auth::id();

        $this->loadMonedas();
        
        $this->fecha = app(FechaOperacionContext::class)->fecha();
    }

    #[On('fecha-operacion-context-changed')]
    public function actualizarFechaOperacionActual(): void
    {
        $fechaOperacion = app(FechaOperacionContext::class)->fecha();
        
        $this->fecha = $fechaOperacion;

    }
    
    public function cancelarEdicionMoneda(): void
    {
        $this->reset([
            'monedaEditandoId',
            'monedaId',
            'monto_esperado_edit',
            'monto_contado_edit',
        ]);

        $this->resetValidation();

        $this->loadMonedas();
    }   
        
    private function loadMonedas(): void
    {
        /*$idMonedasSeleccionadas = collect($this->cierreMonedas)
            ->pluck('moneda_id')
            ->map(fn ($id) => (int) $id)
            ->all();*/
        $idMonedasSeleccionadas = collect($this->cierreMonedas)
            ->pluck('moneda_id')
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === $this->monedaEditandoId)
            ->all();

        $this->monedas = Moneda::ParaSelect()
            ->get()
            ->reject(
                fn (Moneda $moneda) =>
                    in_array((int) $moneda->id, $idMonedasSeleccionadas, true)
            )
            ->map(fn (Moneda $moneda) => [
                'id' => $moneda->id,
                'codigo' => $moneda->codigo,
                'es_principal' => $moneda->es_principal,
                'tasa_cambio' => $moneda->tasa_cambio,
            ])
            ->values()
            ->toArray();
    }


    /*
    |--------------------------------------------------------------------------
    | Agregando CierreCajaMoneda
    |--------------------------------------------------------------------------
    */

    #[On('cierre-caja-moneda-agregar')]
    public function addCierreCajaMoneda(): void
    {
        $this->validate([
            'monedaId' => 'required|integer|exists:monedas,id',
            'monto_esperado_edit' => 'required|numeric|gt:0',
            'monto_contado_edit' => 'required|numeric|gt:0',
        ], [
            'monedaId.required' => 'Debe seleccionar una moneda.',
            'monedaId.exists' => 'La moneda seleccionada no es válida.',
            'monto_esperado_edit.required' => 'El monto esperado es obligatorio.',
            'monto_esperado_edit.numeric' => 'El monto esperado debe ser numérico.',
            'monto_esperado_edit.gt' => 'El monto esperado debe ser mayor que cero.',
            'monto_contado_edit.required' => 'El monto contado es obligatorio.',
            'monto_contado_edit.numeric' => 'El monto contado debe ser numérico.',
            'monto_contado_edit.gt' => 'El monto contado debe ser mayor que cero.',
        ]);

        $monedaId = (int) $this->monedaId;

        if (
            collect($this->cierreMonedas)
                ->contains(fn (array $item) => (int) $item['moneda_id'] === $monedaId)
        ) {
            $this->addError(
                'monedaId',
                'Esta moneda ya está agregada al cierre.'
            );

            return;
        }

        $moneda = Moneda::query()->findOrFail($monedaId);

        $this->cierreMonedas[] = [
            'moneda_id' => $moneda->id,
            'moneda' => $moneda->codigo,
            'monto_esperado' => $this->monto_esperado_edit,
            'monto_contado' => $this->monto_contado_edit,
            'estado' => 'pendiente',
        ];

        $this->reset(
            'monedaId',
            'monto_esperado_edit',
            'monto_contado_edit'
        );

        $this->resetValidation([
            'monedaId',
            'monto_esperado_edit',
            'monto_contado_edit',
        ]);

        $this->loadMonedas();

        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Edición CierreCajaMoneda
    |--------------------------------------------------------------------------
    */

    
    #[On('cierre-caja-moneda-cargar-edicion')]
    public function editCierreCajaMoneda(int $id): void
    {
        $index = collect($this->cierreMonedas)
            ->search(
                fn (array $item) => (int) $item['moneda_id'] === $id
            );

        if ($index === false) {
            return;
        }

        $moneda = $this->cierreMonedas[$index];

        $this->monedaEditandoId = $id;
        $this->monedaId = $id;
        $this->monto_esperado_edit = (string) $moneda['monto_esperado'];
        $this->monto_contado_edit = (string) $moneda['monto_contado'];
        $this->estado = (string) $moneda['estado'];

        $this->loadMonedas();

        $this->resetValidation([
            'monedaId',
            'monto_esperado_edit',
            'monto_contado_edit',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Eliminación CierreCajaMoneda
    |--------------------------------------------------------------------------
    */

    #[On('cierre-caja-moneda-eliminar')]
    public function delCierreCajaMoneda(int $index): void
    {
        if (!array_key_exists($index, $this->cierreMonedas)) {
            return;
        }

        unset($this->cierreMonedas[$index]);

        $this->cierreMonedas = array_values($this->cierreMonedas);

        $this->loadMonedas();

        $this->reset(
            'monedaId',
            'monto_esperado_edit',
            'monto_contado_edit'
        );

        $this->resetValidation();
    }

    /*
    |--------------------------------------------------------------------------
    | Edición Detalles
    |--------------------------------------------------------------------------
    */

    #[On('cierre-caja-moneda-detalles-cargar-edicion')]
    public function editDetalles(int $id): void
    {
        // Avisar al DetallesManager qué cierre-caja se está editando
        $this->dispatch(
            'cierre-caja-moneda-detalles-cargar',
            cierreCajaMonedaId: $id
        );
        /*Log::info(
            'CierreCajaForm - cierre-caja-moneda-detalles-cargar -> editDetalles',
            ['cierreCajaMonedaId' => $id]
        );*/
    }

    /*
    |--------------------------------------------------------------------------
    | Edición
    |--------------------------------------------------------------------------
    */

    #[On('cierre-caja-cargar-edicion')]
    public function edit(int $id): void
    {
        
        try {
            $cierreCaja = CierreCaja::query()
                ->with('cierres_caja_moneda.moneda')
                ->findOrFail($id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('loading-stop');

            throw $e;
        }

        $this->cierreCajaId = $cierreCaja->id;
        $this->localId = $cierreCaja->local_id;
        $this->cajaId = $cierreCaja->caja_id;
        $this->periodoContableId = $cierreCaja->periodo_contable_id;
        $this->operadorId = $cierreCaja->operador_id;
        $this->fecha = $cierreCaja->fecha
                    ? \Carbon\Carbon::parse($cierreCaja->fecha)->format('Y-m-d')
                    : '';

        $this->cierreMonedas = $cierreCaja->cierres_caja_moneda
            ->map(fn ($item) => [
                'moneda_id' => $item->moneda_id,
                'moneda' => $item->moneda->codigo,
                'monto_esperado' => (string) $item->monto_esperado,
                'monto_contado' => (string) $item->monto_contado,
                'estado' => $item->estado,
            ])
            ->values()
            ->toArray();

        $this->reset(
            'monedaId',
            'monto_esperado_edit',
            'monto_contado_edit'
        );

        $this->resetValidation();

        $this->loadMonedas();

        $this->dispatch(
            'cierre-caja-edicion-cargada',
            cierreCajaId: $cierreCaja->id
        );
    }

    #[On('cierre-caja-edicion-cargada')]
    public function cierrCajaEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Cambio de caja
    |--------------------------------------------------------------------------
    */

    // public function updatedCajaId($value): void
    // {
    //     dd('updatedCajaId');
    //     $this->notificarFiltros();
    // }

    public function cajaChanged(): void
    {
        $this->notificarFiltros();
    }
    
    public function aplicarFiltrosResultado()
    {
        $this->notificarFiltros();
    }

    private function notificarFiltros(): void
    {
        // if(!$this->usar_filtros){
        //     $this->dispatch('gasto-limpiar-filtros');
        // }
        
        $this->dispatch('cierre-caja-filtros-actualizados',
            localId: $this->localId,
            periodoContableId: $this->periodoContableId,
            // cajaId: $this->usar_filtros ? $this->cajaId : null,
            cajaId: $this->cajaId,
        );
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

        if ($this->cierreCajaId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $caja = $this->command(
                'cierreCaja.update',
                $payload,
            );

            $this->dispatch(
                'cierre-caja-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Cierre de Caja Actualizado correctamente...',
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

            $cierreCaja = $this->command(
                'cierreCaja.create',
                $this->payload()
            );

            $this->dispatch(
                'cierre-caja-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Cierre de Caja Creado correctamente...',
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
            'cierre-caja-guardado'
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
            'id' => $this->cierreCajaId,
            'local_id' => $this->localId,
            'caja_id' => $this->cajaId,
            'periodo_contable_id' => $this->periodoContableId,
            'operador_id' => $this->operadorId,
            'fecha' => $this->fecha,
            'cierreMonedas' => $this->cierreMonedas,
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
            'cierreCajaId',
            'cajaId',
            'fecha',
            'monedaEditandoId',
            'monedaId',
            'monto_esperado_edit',
            'monto_contado_edit',
            'estado',
            'cierreMonedas',
        ]);

        // Conservar el contexto de trabajo actual.
        $this->localId = app(LocalContext::class)->id();
        $this->periodoContableId = app(PeriodoContext::class)->id();
        $this->operadorId = Auth::id();

        $this->resetValidation();
    }

    public function render()
    {
        $localId = app(LocalContext::class)->id();

        $cajas = Caja::DeLocal($localId)
            ->orderBy('nombre')
            ->get();

        $periodoContableId = app(PeriodoContext::class)->id();
        $periodoActual = PeriodoContable::find($periodoContableId);

        $fechaMinima = $periodoActual
            ? ($periodoActual->fecha_inicio ?? $periodoActual->fecha_inicial ?? null)
            : null;

        $fechaMaxima = $periodoActual
            ? ($periodoActual->fecha_fin ?? $periodoActual->fecha_final ?? null)
            : null;

        return view('modules.finanzas.cierre-caja.form', [
            'cajas' => $cajas,
            'monedas' => $this->monedas,
            'fechaMinima' => $fechaMinima,
            'fechaMaxima' => $fechaMaxima,
        ]);
    }
}