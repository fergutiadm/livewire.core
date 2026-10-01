<?php

namespace App\Modules\Gastos\TipoGasto\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\TipoGasto;
use App\Models\Local;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class TipoGastoForm extends Component
{
    use HasCommands;

    public ?int $tipoGastoId = null;

    public ?int $localId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public bool $fijo = false;

    public bool $activo = true;

    public $locales;

    protected $rules = [
        'localId' => 'required|exists:locales,id',
        'nombre' => 'required|string|max:255',
        'descripcion' => 'string|min:3|max:10',
        ];

    protected function messages(): array
    {
        return [
        'localId.required' =>
            'El Local es obligatorio',

        'nombre.required' => 'El Nombre es obligatorio',

        'descripcion.min' => 'La Descripción debe tener al menos 3 caracteres',
        'descripcion.max' => 'La Descripción no puede tener más de 10 caracteres',
        ];

    }

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->loadData();

        $this->dispatch(
            'tipo-gasto-filtros-actualizados',
            localId: $this->localId,
        );
    }

    public function loadData(): void
    {
        $this->locales = Local::orderBy('nombre')->get();

        if (!$this->localId) {
            $this->localId = $this->locales->first()?->id;
        }

    }

    #[On('tipo-gasto-cargar-edicion')]
    public function edit(int $id): void
    {
        $tipoGasto = TipoGasto::findOrFail($id);

        $this->tipoGastoId = $tipoGasto->id;

        $this->localId = $tipoGasto->local_id;

        $this->monedaId = $tipoGasto->moneda_id;

        $this->nombre = $tipoGasto->nombre;

        $this->descripcion = $tipoGasto->descripcion ?? '';

        $this->fijo = $tipoGasto->fijo;

        $this->activo = $tipoGasto->activo;

        // Avisar que terminó la edición
        $this->dispatch(
            'tipo-gasto-edicion-cargado',
            tipoGastoId: $tipoGasto->id
        );

        $this->resetValidation();
    }

    #[On('tipo-gasto-edicion-cargado')]
    public function tipoGastoEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Cambio de local
    |--------------------------------------------------------------------------
    */

    public function localChanged(): void
    {
        $this->dispatch(
            'tipo-gasto-media-reset'
        );

        $this->dispatch(
            'tipo-gasto-filtros-actualizados',
            localId: $this->localId,
        );
    }

    public function tipoGastoChanged(): void
    {
        $this->dispatch(
            'tipo-gasto-filtros-actualizados',
            localId: $this->localId,
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

        if ($this->tipoGastoId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $tipoGasto = $this->command(
                'tipoGasto.update',
                $payload,
            );

            $this->dispatch(
                'tipoGasto-actualizada',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'tipoGasto Actualizada correctamente...',
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

            $tipoGasto = $this->command(
                'tipoGasto.create',
                $this->payload()
            );

            $this->dispatch(
                'tipoGasto-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'tipoGasto Creada correctamente...',
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
            'tipoGasto-guardada'
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
            'id' => $this->tipoGastoId,

            'local_id' => $this->localId,

            'tipo_gasto_id' => $this->tipoGastoId,

            'moneda_id' => $this->monedaId,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            'fijo' => $this->fijo,

            'activo' => $this->activo,
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
            'tipoGastoId',
            'nombre',
            'descripcion',
            'fijo',
            'activo',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Mantener local
        |--------------------------------------------------------------------------
        */

        if (!$this->localId) {
            $this->localId =
                $this->locales->first()?->id;
        }

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.gastos.tipo-gasto.form');
    }
}