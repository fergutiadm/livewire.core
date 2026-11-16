<?php

namespace App\Modules\Configuracion\Provincia\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Provincia;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Livewire\Component;

class ProvinciaForm extends Component
{
    use HasCommands;

    public ?int $provinciaId = null;

    public string $nombre = '';
    public string $slug = '';
    public bool $activa = true;

    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' =>
                'El Nombre es obligatorio',

            'nombre.max' =>
                'El Nombre es muy largo',

            'slug.required' =>
                'No se pudo generar el identificador de la provincia.',

            'slug.regex' =>
                'El identificador contiene caracteres no permitidos.',

            'slug.max' =>
                'El identificador no puede superar los 160 caracteres.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Cambios del formulario
    |--------------------------------------------------------------------------
    */

    public function updatedNombre(string $value): void
    {
        // El slug de una Provincia existente permanece estable.
        if ($this->provinciaId !== null) {
            return;
        }

        $this->slug = Str::slug($value);
    }

    #[On('provincia-cargar-edicion')]
    public function edit(int $id): void
    {
        $provincia = Provincia::findOrFail($id);

        $this->provinciaId = $provincia->id;

        $this->nombre = $provincia->nombre;

        $this->slug = $provincia->slug;

        $this->activa = (bool) $provincia->activa;

        // Avisar que terminó la edición
        $this->dispatch(
            'provincia-edicion-cargado',
            provinciaId: $provincia->id
        );

        $this->resetValidation();
    }

    #[On('provincia-edicion-cargado')]
    public function provinciaEdicionCargada(): void
    {
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
            $this->normalizarFormulario();

            $this->validate();

            /*
            |--------------------------------------------------------------------------
            | UPDATE
            |--------------------------------------------------------------------------
            */

            if ($this->provinciaId) {

                $payload = $this->payload();

                //Logger()->info("ACTUALIZANDO provincia", ['payload'=>$payload]);

                $provincia = $this->command(
                    'provincia.update',
                    $payload,
                );

                $this->dispatch(
                    'provincia-actualizado',
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Provincia actualizada correctamente...',
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

                $provincia = $this->command(
                    'provincia.create',
                    $this->payload()
                );

                $this->dispatch(
                    'provincia-creado'
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Provincia creada correctamente...',
                        'type' => 'success',
                    ]
                );
            }
        } catch (\Illuminate\Validation\ValidationException $e) {

            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }

            return;

        }finally {

            $this->dispatch('loading-stop');
        }

        /*
        |--------------------------------------------------------------------------
        | Notificar página
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'provincia-guardado'
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
    | Normalización
    |--------------------------------------------------------------------------
    */

    private function normalizarFormulario(): void
    {
        $this->nombre = trim($this->nombre);

        if ($this->provinciaId === null) {

            $this->slug = Str::slug($this->nombre);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Payload CQRS
    |--------------------------------------------------------------------------
    */

    private function payload(): array
    {
        return [
            'id' => $this->provinciaId,

            'nombre' => $this->nombre,

            'slug' => $this->slug,

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
            'provinciaId',
            'nombre',
            'slug',
            'activa',
        ]);


        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.configuracion.provincia.form');
    }
}