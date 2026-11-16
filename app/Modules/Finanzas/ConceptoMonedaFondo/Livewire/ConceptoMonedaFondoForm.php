<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\ConceptoMonedaFondo;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ConceptoMonedaFondoForm extends Component
{
    use HasCommands;

    public ?int $conceptoMonedaFondoId = null;

    public string $nombre = '';
    public string $slug = '';
    public bool $entrada_salida = true;
    public bool $activo = false;
    public string $tipo = '';

    public array $tipos = [
        ['id' => ConceptoMonedaFondo::TIPO_ESTANDAR,  'nombre' => 'Estandar'],
        ['id' => ConceptoMonedaFondo::TIPO_OPERACIONAL,   'nombre' => 'Operacional'],
    ];

    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'tipo' => [
                    'nullable',
                    'string',
                    Rule::in([
                        ConceptoMonedaFondo::TIPO_ESTANDAR,
                        ConceptoMonedaFondo::TIPO_OPERACIONAL,
                    ]),],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' =>
                'El Nombre es obligatorio',

            'tipo.in' => 'El tipo seleccionado no es válido.',

            'slug.required' =>
                'No se pudo generar el identificador del concepto.',

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
        // El slug de un conceptoMonedaFondo existente permanece estable.
        if ($this->conceptoMonedaFondoId !== null) {
            return;
        }

        $this->slug = Str::slug($value);
    }

    #[On('concepto-moneda-fondo-cargar-edicion')]
    public function edit(int $id): void
    {
        $conceptoMonedaFondo = ConceptoMonedaFondo::findOrFail($id);

        $this->conceptoMonedaFondoId = $conceptoMonedaFondo->id;

        $this->nombre = $conceptoMonedaFondo->nombre;

        $this->tipo = $conceptoMonedaFondo->tipo ?? ConceptoMonedaFondo::TIPO_ESTANDAR;

        $this->slug = $conceptoMonedaFondo->slug;

        $this->entrada_salida = (bool) $conceptoMonedaFondo->entrada_salida;

        $this->activo = (bool) $conceptoMonedaFondo->activo;

        // Avisar que terminó la edición
        $this->dispatch(
            'concepto-moneda-fondo-edicion-cargado',
            conceptoMonedaFondoId: $conceptoMonedaFondo->id
        );

        $this->resetValidation();
    }

    #[On('concepto-moneda-fondo-edicion-cargado')]
    public function conceptoMonedaFondoEdicionCargada(): void
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

            if ($this->conceptoMonedaFondoId) {

                $payload = $this->payload();

                //Logger()->info("ACTUALIZANDO ConceptoMonedaFondo", ['payload'=>$payload]);

                $conceptoMonedaFondo = $this->command(
                    'conceptoMonedaFondo.update',
                    $payload,
                );

                $this->dispatch(
                    'concepto-moneda-fondo-actualizado',
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Concepto actualizado correctamente...',
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

                $conceptoMonedaFondo = $this->command(
                    'conceptoMonedaFondo.create',
                    $this->payload()
                );

                $this->dispatch(
                    'concepto-moneda-fondo-creado'
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Concepto creado correctamente...',
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
            'concepto-moneda-fondo-guardado'
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

        if ($this->conceptoMonedaFondoId === null) {

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
            'id' => $this->conceptoMonedaFondoId,

            'concepto_moneda_fondo_id' => $this->conceptoMonedaFondoId,

            'nombre' => $this->nombre,

            'tipo' => $this->tipo,

            'slug' => $this->slug,

            'entrada_salida' => $this->entrada_salida,

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
            'conceptoMonedaFondoId',
            'nombre',
            'tipo',
            'entrada_salida',
            'activo',
        ]);


        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.finanzas.concepto-moneda-fondo.form');
    }
}