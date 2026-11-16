<?php

namespace App\Modules\Finanzas\Banco\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Banco;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Livewire\Attributes\On;
use Livewire\Component;

class BancoForm extends Component
{
    use HasCommands;

    public ?int $bancoId = null;
    public ?int $provinciaId = null;

    public string $nombre = '';
    public string $alias = '';
    public ?int $municipio_id = null;
    public string $codigo_swift = '';
    public string $direccion = '';
    public string $telefonos = '';
    public bool $activo = true;

    public array $municipios = [];

    public bool $usar_filtros = true;

    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'alias' => ['nullable', 'string', 'max:50'],
            'municipio_id' => ['required', 'integer', 'exists:municipios,id'],
            'codigo_swift' => ['required', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefonos' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' =>
                'El Nombre es obligatorio',

            'nombre.max' =>
                'El Nombre es muy largo',

            'alias.max' =>
                'El Alias es muy largo',

            'municipio_id.required' =>
                'El municipio seleccionado no existe',

            'codigo_swift.required' =>
                'El Código Swift es obligatorio',

            'codigo_swift.max' =>
                'El Código Swift es muy largo',

            'direccion.max' =>
                'La Dirección es muy largo',

            'telefonos.max' =>
                'Demasiados Teléfonos',
        ];
    }

    public function mount(SetupConfigService $config)
    {
        $provincia_default = $config->get('provincia_default');
        $this->provinciaId = Provincia::query()->where('slug', $provincia_default)->value('id')??null;

        $this->cargarMunicipios();

        $municipio_default = app(SetupConfigService::class)->get('municipio_default', '');
        $this->municipio_id = Municipio::query()->where('slug', $municipio_default)->value('id')??null;
    }

    public function updatedProvinciaId(): void
    {
        $this->municipio_id = null;

        $this->cargarMunicipios();

        $this->resetValidation([
            'provinciaId',
            'municipio_id',
        ]);

        $this->notificarFiltros();
    }

    private function cargarMunicipios(): void
    {
        if (!$this->provinciaId) {
            $this->municipios = [];
            return;
        }

        $this->municipios = Municipio::query()
            ->activos()
            ->where('provincia_id', $this->provinciaId)
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
            ])
            ->toArray();
    }

    public function updatedMunicipioId(): void
    {
        $this->resetValidation([
            'provinciaId',
            'municipio_id',
        ]);

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
            municipioId: $this->usar_filtros ? $this->municipio_id : null,

            // fecha: $this->usar_filtros ? $this->fecha : null,
        );
    }

    #[On('banco-cargar-edicion')]
    public function edit(int $id): void
    {
        $banco = Banco::findOrFail($id);

        $this->bancoId = $banco->id;

        $municipio = Municipio::findOrFail($banco->municipio_id);

        $this->provinciaId = $municipio->provincia_id;

        $this->nombre = $banco->nombre;
        $this->alias = $banco->alias;
        $this->codigo_swift = $banco->codigo_swift;
        $this->direccion = $banco->direccion;
        $this->telefonos = $banco->telefonos;
        $this->activo = (bool) $banco->activo;

        /*
        |--------------------------------------------------------------------------
        | Primero cargar las opciones del municipio
        |--------------------------------------------------------------------------
        */

        $this->cargarMunicipios();

        /*
        |--------------------------------------------------------------------------
        | Finalmente establecer el municipio seleccionado
        |--------------------------------------------------------------------------
        */

        $this->municipio_id = (int) $banco->municipio_id;

        $this->dispatch(
            'banco-edicion-cargado',
            bancoId: $banco->id
        );

        $this->resetValidation();
    }

    #[On('banco-edicion-cargado')]
    public function bancoEdicionCargada(): void
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

            /*
            |--------------------------------------------------------------------------
            | UPDATE
            |--------------------------------------------------------------------------
            */

            if ($this->bancoId) {

                $payload = $this->payload();

                $banco = $this->command(
                    'banco.update',
                    $payload,
                );

                $this->dispatch(
                    'banco-actualizado',
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Banco actualizado correctamente...',
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

                $banco = $this->command(
                    'banco.create',
                    $this->payload()
                );

                $this->dispatch(
                    'banco-creado'
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Banco creado correctamente...',
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
            'banco-guardado'
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
            'id' => $this->bancoId,

            'nombre' => $this->nombre,

            'alias' => $this->alias,

            'municipio_id' => $this->municipio_id,

            'codigo_swift' => $this->codigo_swift,

            'direccion' => $this->direccion,

            'telefonos' => $this->telefonos,

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
            'bancoId',
            'provinciaId',
            'nombre',
            'alias',
            'municipio_id',
            'codigo_swift',
            'direccion',
            'telefonos',
            'activo',
        ]);


        $this->resetValidation();
    }

    public function render(SetupConfigService $config)
    {
        $provincias = Provincia::all();

        return view('modules.finanzas.banco.form', [
            'provincias' => $provincias,
        ]);
    }
}