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

    public $color_bg = 'bg-white';  // Default color
    public $color_text = 'text-black'; // Default color

    public array $palette = [
        ['bg' => 'bg-white',      'text' => 'text-black'],
        ['bg' => 'bg-blue-500',   'text' => 'text-white'],
        ['bg' => 'bg-green-500',  'text' => 'text-white'],
        ['bg' => 'bg-yellow-500', 'text' => 'text-black'],
        ['bg' => 'bg-purple-500', 'text' => 'text-white'],
        ['bg' => 'bg-pink-500',   'text' => 'text-white'],
        ['bg' => 'bg-indigo-500', 'text' => 'text-white'],
        ['bg' => 'bg-teal-500',   'text' => 'text-white'],
        ['bg' => 'bg-orange-500', 'text' => 'text-black'],
        ['bg' => 'bg-cyan-500',   'text' => 'text-black'],
        ['bg' => 'bg-black',      'text' => 'text-white'],
    ];

    protected $rules = [
        'localId' => 'required|exists:locales,id',
        'nombre' => 'required|string|max:50',
        'descripcion' => 'string|min:3|max:200',
        ];

    protected function messages(): array
    {
        return [
        'localId.required' =>
            'El Local es obligatorio',

        'nombre.required' => 'El Nombre es obligatorio',

        'descripcion.min' => 'La Descripción debe tener al menos 3 caracteres',
        'descripcion.max' => 'La Descripción no puede tener más de 200 caracteres',
        ];

    }

    public function selectPalette(string $bg, string $text): void
    {
        $this->color_bg = $bg;
        $this->color_text = $text;
    }

    private function paletteIsValid(): bool
    {
        return collect($this->palette)->contains(fn ($p) =>
            $p['bg'] === $this->color_bg && $p['text'] === $this->color_text
        );
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
        $this->nombre = $tipoGasto->nombre;
        $this->color_bg = $tipoGasto->color_bg;
        $this->color_text = $tipoGasto->color_text;
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
        Log::info("EVENTO - tipo-gasto-edicion-cargado");
        $this->dispatch('loading-stop');
    }

    /*
    |--------------------------------------------------------------------------
    | Cambio de local
    |--------------------------------------------------------------------------
    */

    /*public function localChanged(): void
    {   
        $this->dispatch(
            'loading-start',
            message: 'Cargando tipos de gastos...'
        );

        $this->dispatch(
            'tipo-gasto-filtros-actualizados',
            localId: $this->localId,
        );
    }*/

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

            Logger()->info("ACTUALIZANDO TIPO DE GASTO", ['payload'=>$payload]);

            $tipoGasto = $this->command(
                'tipoGasto.update',
                $payload,
            );

            $this->dispatch(
                'tipo-gasto-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Tipo de Gasto Actualizado correctamente...',
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
                'tipo-gasto-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Tipo de Gasto Creado correctamente...',
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
            'tipo-gasto-guardado'
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

            'nombre' => $this->nombre,

            'color_bg' => $this->color_bg,

            'color_text' => $this->color_text,

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
            'color_bg',
            'color_text',
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