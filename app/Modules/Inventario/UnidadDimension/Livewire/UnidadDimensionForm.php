<?php

namespace App\Modules\Inventario\UnidadDimension\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\UnidadDimension;
use Livewire\Attributes\On;
use Livewire\Component;

class UnidadDimensionForm extends Component
{
    use HasCommands;

    public ?int $unidadDimensionId = null;

    public string $codigo = '';
    public string $nombre = '';
    public string $descripcion = '';
    public bool $activa = false;

    protected $rules = [
        'codigo' => 'required|string|max:30',
        
        'nombre' => 'required|string|max:100',

        'descripcion' => 'nullable|string|min:3|max:255',
    ];

    protected function messages(): array
    {
        return [
            'codigo.required' =>
                'El Código es obligatorio',

            'codigo.max' =>
                'El Código es muy largo',

            'nombre.required' =>
                'El Nombre es obligatorio',

            'nombre.max' =>
                'El Nombre es muy largo',

            'descripcion.min' =>
                'La descripción debe tener al menos 3 caracteres',

            'descripcion.max' =>
                'Descripción demasiado larga',
        ];
    }

    #[On('unidad-dimension-cargar-edicion')]
    public function edit(int $id): void
    {
        $unidadDimension = UnidadDimension::findOrFail($id);

        $this->unidadDimensionId = $unidadDimension->id;

        $this->codigo = $unidadDimension->codigo;

        $this->nombre = $unidadDimension->nombre;

        $this->descripcion = $unidadDimension->descripcion ?? '';

        $this->activa = (bool) $unidadDimension->activa;


        // Avisar al MediaManager qué categoría se está editando
        // $this->dispatch(
        //     'unidad-dimension-media-cargar',
        //     unidadDimensionId: $unidadDimension->id
        // );

        // Avisar que terminó la edición
        $this->dispatch(
            'unidad-dimension-edicion-cargado',
            unidadDimensionId: $unidadDimension->id
        );

        $this->resetValidation();
    }

    #[On('unidad-dimension-edicion-cargado')]
    public function unidadDimensionEdicionCargada(): void
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

        if ($this->unidadDimensionId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO Unidad Dimension", ['payload'=>$payload]);

            $unidadDimension = $this->command(
                'unidadDimension.update',
                $payload,
            );

            $this->dispatch(
                'unidad-dimension-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Unidad Dimension Actualizado correctamente...',
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

            $unidadDimension = $this->command(
                'unidadDimension.create',
                $this->payload()
            );

            $this->dispatch(
                'unidad-dimension-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Unidad Dimension Creado correctamente...',
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
            'unidad-dimension-guardado'
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
            'id' => $this->unidadDimensionId,

            'unidad_dimension_id' => $this->unidadDimensionId,

            'codigo' => $this->codigo,

            'nombre' => $this->nombre,

            'descripcion' => $this->descripcion,

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
        // Log::info('Listener reset-form', [
        //     'unidadDimensionId' => $this->unidadDimensionId,
        // ]);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'unidadDimensionId',
            'codigo',
            'nombre',
            'descripcion',
            'activa',
        ]);


        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.inventario.unidad-dimension.form');
    }
}