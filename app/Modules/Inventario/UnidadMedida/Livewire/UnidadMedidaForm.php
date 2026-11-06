<?php

namespace App\Modules\Inventario\UnidadMedida\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\UnidadDimension;
use App\Models\UnidadMedida;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UnidadMedidaForm extends Component
{
    use HasCommands;

    public ?int $unidadMedidaId = null;
    
    public ?int $unidadDimensionId = null;
    public string $codigo = '';
    public string $nombre = '';
    public string $abreviatura = '';
    public string $tipo = '';
    public string $factor_base = '';
    public bool $es_base_dimension = false;
    public bool $activa = false;

    public Collection $unidadDimensiones;

    public array $tipos = [
        ['id' => UnidadMedida::TIPO_ESTANDAR,  'nombre' => 'Estandar'],
        ['id' => UnidadMedida::TIPO_OPERACIONAL,   'nombre' => 'Operacional'],
    ];

    protected function rules(): array
    {
        return [
            'unidadDimensionId' => [
                'required', 
                Rule::exists('unidad_dimenciones', 'unidad_dimension_id')
            ],
            'codigo' => ['required', 'string', 'max:30'],
            
            'nombre' => ['required', 'string', 'max:100'],

            'abreviatura' => ['nullable', 'string', 'min:3', 'max:20'],

            'tipo' => [
                'nullable', 
                'string',
                Rule::in([
                    UnidadMedida::TIPO_ESTANDAR,
                    UnidadMedida::TIPO_OPERACIONAL,
                ]),],

            'factor_base' => ['nullable', 'numeric', 'min:0'],
        ];
    }

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

            'abreviatura.min' =>
                'La descripción debe tener al menos 3 caracteres',

            'abreviatura.max' =>
                'Descripción demasiado larga',
                
            'tipo.in' => 'El tipo seleccionado no es válido.',

            'factor_base.numeric' => 'El tiene que ser un numero mayor que cero.',

            'factor_base.min' => 'El tiene que ser un numero mayor que cero.',
        ];
    }

    public function mount(?int $unidadDimensionId = null): void
    {
        $this->unidadDimensiones = UnidadDimension::query()
            ->where('activa', true)
            ->orWhere('id', $unidadDimensionId)
            ->orderBy('nombre')
            ->get();

        $this->unidadDimensionId ??= $this->unidadDimensiones->first()?->id;

        // $this->notificarFiltros();
    }

    /**
     * HOOKS AUTOMÁTICOS DE LIVEWIRE: Reemplazan a los antiguos wire:change
     * Se ejecutan solos cuando cambias el select en la vista
     */
    //Averiguar pq no funciona
    // public function updatedUnidadDimensionId($value): void
    // {
    //     $this->notificarFiltros();
    // }

    public function unidadDimensionChanged(): void
    {
        $this->notificarFiltros();
    }

    private function notificarFiltros(): void
    {
        // if(!$this->usar_filtros){
        //     $this->dispatch('unidad-medida-limpiar-filtros');
        // }

        $this->dispatch('unidad-medida-filtros-actualizados',
            unidadDimensionId: $this->unidadDimensionId,
        );
    }

    #[On('unidad-medida-cargar-edicion')]
    public function edit(int $id): void
    {
        $unidadMedida = UnidadMedida::findOrFail($id);

        $this->unidadMedidaId = $unidadMedida->id;

        $this->unidadDimensionId = $unidadMedida->unidad_dimension_id;

        $this->codigo = $unidadMedida->codigo;

        $this->nombre = $unidadMedida->nombre;

        $this->abreviatura = $unidadMedida->abreviatura ?? '';

        $this->tipo = $unidadMedida->tipo ?? UnidadMedida::TIPO_ESTANDAR;

        $this->factor_base = $unidadMedida->factor_base;

        $this->es_base_dimension = (bool) $unidadMedida->es_base_dimension;

        $this->activa = (bool) $unidadMedida->activa;


        // Avisar que terminó la edición
        $this->dispatch(
            'unidad-medida-edicion-cargado',
            unidadMedidaId: $unidadMedida->id
        );

        $this->resetValidation();
    }

    #[On('unidad-medida-edicion-cargado')]
    public function unidadMedidaEdicionCargada(): void
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

        if ($this->unidadMedidaId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO Unidad Medida", ['payload'=>$payload]);

            $unidadMedida = $this->command(
                'unidadMedida.update',
                $payload,
            );

            $this->dispatch(
                'unidad-medida-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Unidad Medida Actualizado correctamente...',
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

            $unidadMedida = $this->command(
                'unidadMedida.create',
                $this->payload()
            );

            $this->dispatch(
                'unidad-medida-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Unidad Medida Creado correctamente...',
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
            'unidad-medida-guardado'
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
            'id' => $this->unidadMedidaId,

            'unidad_medida_id' => $this->unidadMedidaId,

            'codigo' => $this->codigo,

            'nombre' => $this->nombre,

            'abreviatura' => $this->abreviatura,

            'tipo' => $this->tipo,

            'factor_base' => $this->factor_base,

            'es_base_dimension' => $this->es_base_dimension,

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
        //     'unidadMedidaId' => $this->unidadMedidaId,
        // ]);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'unidadMedidaId',
            'unidadDimensionId',
            'codigo',
            'nombre',
            'abreviatura',
            'tipo',
            'factor_base',
            'es_base_dimension',
            'activa',
        ]);


        $this->resetValidation();
    }

    public function render()
    {
        if(!$this->unidadDimensionId){
            $this->unidadDimensionId = $this->unidadDimensiones->first()->id;
            $this->notificarFiltros();
        }

        return view('modules.inventario.unidad-medida.form');
    }
}