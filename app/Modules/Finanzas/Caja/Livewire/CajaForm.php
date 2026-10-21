<?php

namespace App\Modules\Finanzas\Caja\Livewire;

use App\Core\Context\LocalContext;
use App\Core\CQRS\HasCommands;
use App\Models\Caja;
use App\Models\Local;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CajaForm extends Component
{
    use HasCommands;

    public ?int $cajaId = null;

    public ?int $localId = null;

    public string $codigo = '';

    public string $nombre = '';

    public bool $activa = true;

    protected $rules = [
        'codigo' => 'required|string|min:3|max:10',
        'nombre' => 'required|string|max:255',
        'localId' => 'required|exists:locales,id',
        ];

    protected function messages(): array
    {
        return [
            'codigo.required' => 'El Código es obligatorio',
            'codigo.min' => 'El Código debe tener al menos 3 caracteres',
            'codigo.max' => 'El Código no puede tener más de 10 caracteres',
            
            'nombre.required' => 'El Nombre es obligatorio',

            'localId.required' =>
                'El Local es obligatorio',
            'localId.exists' =>
                'El Local seleccionado no es válido',
        ];

    }

    public function mount(): void
    {
        $this->localId = Local::orderBy('nombre')->value('id');
    }

    #[On('caja-cargar-edicion')]
    public function edit(int $id): void
    {
        $caja = Caja::findOrFail($id);

        $this->cajaId = $caja->id;

        $this->localId = $caja->localId;

        $this->codigo = $caja->codigo ?? '';

        $this->nombre = $caja->nombre;

        // Avisar que terminó la edición
        $this->dispatch(
            'caja-edicion-cargada',
            cajaId: $caja->id
        );

        $this->resetValidation();
    }

    #[On('caja-edicion-cargada')]
    public function cajaEdicionCargada(): void
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
            'loading-start',
            message: 'Cargando cajas...'
        );
        
        $this->resetForm();

        // Actualizar la tabla.
        $this->dispatch(
            'caja-filtros-actualizados',
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

        if ($this->cajaId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $caja = $this->command(
                'caja.update',
                $payload,
            );

            $this->dispatch(
                'caja-actualizada',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Caja Actualizada correctamente...',
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

            $caja = $this->command(
                'caja.create',
                $this->payload()
            );

            $this->dispatch(
                'caja-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Caja Creada correctamente...',
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
            'caja-guardada'
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
            'id' => $this->cajaId,

            'local_id' => $this->localId,
            
            'codigo' => $this->codigo,

            'nombre' => $this->nombre,

            'activa' => $this->activa,
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
            'cajaId',
            'localId',
            'codigo',
            'nombre',
            'activa',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        $locales = Local::orderBy('nombre')->get();

        if (!$this->localId) {
            $this->localId = $locales->first()?->id;
        }

        return view('modules.finanzas.caja.form',
                ['locales' => $locales]
            );
    }
}
