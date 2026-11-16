<?php

namespace App\Modules\Finanzas\BancoSucursal\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Banco;
use App\Models\BancoSucursal;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Livewire\Attributes\On;
use Livewire\Component;

class BancoSucursalForm extends Component
{
    use HasCommands;

    public ?int $sucursalId = null;
    public ?int $provinciaId = null;

    public ?int $banco_id = null;
    public ?int $municipio_id = null;
    public string $nombre = '';
    public string $codigo = '';
    public string $direccion = '';
    public string $telefonos = '';
    public bool $activa = false;

    public array $municipios = [];
    public array $bancos = [];

    public bool $usar_filtros = true;

    protected function rules(): array
    {
        return [
            'banco_id' => ['required', 'integer', 'exists:bancos,id'],
            'municipio_id' => ['required', 'integer', 'exists:municipios,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'codigo' => ['required', 'string', 'max:150'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefonos' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function messages(): array
    {
        return [
            'banco_id.exists' =>
                'El banco seleccionado no existe',

            'municipio_id.exists' =>
                'El municipio seleccionado no existe',

            'nombre.required' =>
                'El Nombre es obligatorio',

            'nombre.max' =>
                'El Nombre es muy largo',

            'codigo.required' =>
                'El Código es obligatorio',

            'codigo.max' =>
                'El Código es muy largo',

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
        $this->cargarBancos();

        $municipio_default = $config->get('municipio_default', '');
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

    private function cargarBancos(): void
    {
        $this->bancos = Banco::query()
            ->activas()
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

    private function cargarMunicipios(?int $municipioId = null): void
    {
        $provinciaId = null;

        if (!$municipioId) {
            $municipio_default = app(SetupConfigService::class)->get('municipio_default', '');
            $provinciaId = Municipio::query()->where('slug', $municipio_default)->value('provincia_id')??null;
        }else{
            $provinciaId = Municipio::query()->find($municipioId)->value('provincia_id')??null;
        }

        $this->municipios = Municipio::query()
            ->activas()
            ->when(
                $provinciaId,
                fn ($q) => $q->where(
                    'provincia_id',
                    $provinciaId
                )
            )
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
            ])
            ->toArray();
    }

    #[On('banco-sucursal-cargar-edicion')]
    public function edit(int $id): void
    {
        $sucursal = BancoSucursal::findOrFail($id);

        $this->sucursalId = $sucursal->id;

        $this->nombre = $sucursal->nombre;

        $this->banco_id = (int) $sucursal->banco_id;

        $this->codigo = $sucursal->codigo;

        $this->direccion = $sucursal->direccion;

        $this->telefonos = $sucursal->telefonos;

        $this->activa = (bool) $sucursal->activa;

        $this->cargarMunicipios($sucursal->municipio_id);

        $this->municipio_id = (int) $sucursal->municipio_id;

        // Avisar que terminó la edición
        $this->dispatch(
            'banco-sucursal-edicion-cargado',
            sucursalId: $sucursal->id
        );

        $this->resetValidation();
    }

    #[On('banco-sucursal-edicion-cargado')]
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

            if ($this->sucursalId) {

                $payload = $this->payload();

                //Logger()->info("ACTUALIZANDO Banco", ['payload'=>$payload]);

                $sucursal = $this->command(
                    'bancoSucursal.update',
                    $payload,
                );

                $this->dispatch(
                    'banco-sucursal-actualizado',
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Sucursal actualizada correctamente...',
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

                $sucursal = $this->command(
                    'bancoSucursal.create',
                    $this->payload()
                );

                $this->dispatch(
                    'banco-sucursal-creado'
                );

                $this->dispatch(
                    'livewire:alert',
                    [
                        'message' =>
                            'Sucursal creada correctamente...',
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
            'banco-sucursal-guardado'
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
            'id' => $this->sucursalId,

            'nombre' => $this->nombre,

            'municipio_id' => $this->municipio_id,

            'banco_id' => $this->banco_id,

            'codigo' => $this->codigo,

            'direccion' => $this->direccion,

            'telefonos' => $this->telefonos,

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
            'sucursalId',
            'nombre',
            'banco_id',
            'municipio_id',
            'codigo',
            'direccion',
            'telefonos',
            'activa',
        ]);


        $this->resetValidation();
    }

    public function render()
    {
        $provincias = Provincia::all();

        return view('modules.finanzas.banco-sucursal.form', [
            'provincias' => $provincias,
        ]);
    }
}