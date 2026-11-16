<?php

namespace App\Modules\Configuracion\Municipio\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

use Illuminate\Support\Str;

class MunicipioForm extends Component
{
    use HasCommands;

    public ?int $municipioId = null;

    public string $nombre = '';
    public ?int $provincia_id = null;
    public string $slug = '';
    public bool $activo = true;

    public bool $usar_filtros = true;

    protected function rules(): array
    {
        return [
            'provincia_id' => ['required', 'integer', 'exists:provincias,id'],
            'nombre' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }

    protected function messages(): array
    {
        return [
            'provincia_id.required' =>
                'La provincia seleccionada no existe',

            'nombre.required' =>
                'El Nombre es obligatorio',

            'nombre.max' =>
                'El Nombre es muy largo',

            'slug.required' =>
                'No se pudo generar el identificador de la municipio.',

            'slug.regex' =>
                'El identificador contiene caracteres no permitidos.',

            'slug.max' =>
                'El identificador no puede superar los 160 caracteres.',
        ];
    }

    public function mount(SetupConfigService $config)
    {
        $provincia_default = $config->get('provincia_default');
        $this->provincia_id = Provincia::query()->where('slug', $provincia_default)->value('id')??null;
    }

    /*
    |--------------------------------------------------------------------------
    | Cambios del formulario
    |--------------------------------------------------------------------------
    */

    public function updatedProvinciaId()
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
            $this->dispatch('banco-limpiar-filtros');
        }

        $this->dispatch('banco-filtros-actualizados',
            provinciaId: $this->usar_filtros ? $this->provinciaId : null,

            // fecha: $this->usar_filtros ? $this->fecha : null,
        );
    }

    public function updatedNombre(string $value): void
    {
        // El slug de un Municipio existente permanece estable.
        if ($this->municipioId !== null) {
            return;
        }

        $this->slug = Str::slug($value);
    }

    #[On('municipio-cargar-edicion')]
    public function edit(int $id): void
    {
        $municipio = Municipio::findOrFail($id);

        $this->municipioId = $municipio->id;

        $this->provincia_id = $municipio->provincia_id;

        $this->nombre = $municipio->nombre;

        $this->slug = $municipio->slug;

        $this->activo = (bool) $municipio->activo;

        // Avisar que terminó la edición
        $this->dispatch(
            'municipio-edicion-cargado',
            municipioId: $municipio->id
        );

        $this->resetValidation();
    }

    #[On('municipio-edicion-cargado')]
    public function municipioEdicionCargada(): void
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

            if ($this->municipioId) {

                $payload = $this->payload();

                //Logger()->info("ACTUALIZANDO Municipio", ['payload'=>$payload]);

                $municipio = $this->command(
                    'municipio.update',
                    $payload,
                );

                $this->dispatch(
                    'municipio-actualizado',
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Municipio actualizado correctamente...',
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

                $municipio = $this->command(
                    'municipio.create',
                    $this->payload()
                );

                $this->dispatch(
                    'municipio-creado'
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Municipio creado correctamente...',
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



            Log::info('!!!ERROR - CREANDO MUNICIPIO', $e->errors());

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
            'municipio-guardado'
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

        if ($this->municipioId === null) {

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
            'id' => $this->municipioId,

            'provincia_id' => $this->provincia_id,

            'nombre' => $this->nombre,

            'slug' => $this->slug,

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
            'municipioId',
            // 'provincia_id',
            'nombre',
            'slug',
            'activo',
        ]);

        $provincia_default = app(SetupConfigService::class)->get('provincia_default');
        $this->provincia_id = Provincia::query()->where('slug', $provincia_default)->value('id')??null;

        $this->notificarFiltros();

        $this->resetValidation();
    }

    public function render()
    {
        $provincias = Provincia::query()
                            ->activas()
                            ->orderBy("orden")
                            ->get();
        return view('modules.configuracion.municipio.form',[
            'provincias' => $provincias
        ]);
    }
}