<?php

namespace App\Modules\Inventario\Atributo\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Atributo;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class AtributoForm extends Component
{
    use HasCommands;

    public ?int $atributoId = null;

    public string $nombre = '';

    public string $descripcion = '';

    public int $orden_visual = 1;

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
        'nombre' => 'required|string|max:255',

        'descripcion' => 'nullable|string|min:3|max:255',
    ];

    protected function messages(): array
    {
        return [
            'nombre.required' =>
                'El Nombre es obligatorio',

            'descripcion.min' =>
                'La descripción debe tener al menos 3 caracteres',

            'descripcion.max' =>
                'Descripción demasiado larga',
        ];
    }

    #[On('atributo-cargar-edicion')]
    public function edit(int $id): void
    {
        $atributo = Atributo::findOrFail($id);

        $this->atributoId = $atributo->id;

        $this->nombre = $atributo->nombre;

        $this->descripcion = $atributo->descripcion ?? '';

        $this->orden_visual = (int) $atributo->orden_visual;

        // Avisar que terminó la edición
        $this->dispatch(
            'atributo-edicion-cargado',
            atributoId: $atributo->id
        );

        $this->resetValidation();
    }

    #[On('atributo-edicion-cargado')]
    public function atributoEdicionCargado(): void
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

        if ($this->atributoId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $atributo = $this->command(
                'atributo.update',
                $payload,
            );

            $this->dispatch(
                'atributo-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Atributo Actualizado correctamente...',
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

            $atributo = $this->command(
                'atributo.create',
                $this->payload()
            );

            $this->dispatch(
                'atributo-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Atributo Creado correctamente...',
                    'type' => 'success',
                ]
            );

            $this->dispatch('loading-stop');
        }

        /*
        |--------------------------------------------------------------------------
        | Notificar página
        |--------------------------------------------------------------------------
        */

        $this->dispatch(
            'atributo-guardado'
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
            'id' => $this->atributoId,

            'atributo_id' => $this->atributoId,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

            'orden_visual' => $this->orden_visual,
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
        Log::info('Listener reset-form', [
            'atributoId' => $this->atributoId,
        ]);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'atributoId',
            'nombre',
            'descripcion',
            'orden_visual',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.inventario.atributo.form');
    }
}
