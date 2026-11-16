<?php

namespace App\Modules\Finanzas\CuentaBancaria\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Banco;
use App\Models\BancoSucursal;
use App\Models\CuentaBancaria;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use App\Services\MonedaService;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class CuentaBancariaForm extends Component
{
    use HasCommands;

    public ?int $cuentaBancariaId = null;

    public ?int $provinciaId = null;
    public ?int $municipioId = null;
    public ?int $bancoId = null;

    public ?int $sucursal_id = null;
    public ?int $moneda_id = null;

    public string $numero_cuenta = '';
    public string $titular = '';
    public string $descripcion = '';

    public bool $activa = false;
    public bool $usar_filtros = true;

    public array $municipios = [];
    public array $bancos = [];
    public array $sucursales = [];
    public array $monedas = [];

    protected function rules(): array
    {
        return [
            'sucursal_id' => [
                'required',
                'integer',
                'exists:banco_sucursales,id',
            ],
            'moneda_id' => [
                'required',
                'integer',
                'exists:monedas,id',
            ],
            'numero_cuenta' => [
                'required',
                'string',
                'max:150',
            ],
            'titular' => [
                'required',
                'string',
                'max:150',
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'sucursal_id.exists' => 'La sucursal seleccionada no existe.',
            'moneda_id.required' => 'Debe seleccionar una moneda.',
            'moneda_id.exists' => 'La moneda seleccionada no existe.',
            'numero_cuenta.required' => 'El número de cuenta es obligatorio.',
            'numero_cuenta.max' => 'El número de cuenta es demasiado largo.',
            'titular.required' => 'El titular es obligatorio.',
            'titular.max' => 'El titular es demasiado largo.',
            'descripcion.max' => 'La descripción es demasiado larga.',
        ];
    }

    public function mount(SetupConfigService $config): void
    {
        $this->cargarMonedas();
        $this->inicializarSelectores($config);
    }

    /**
     * Inicializa la jerarquía de selección utilizando
     * los valores predeterminados de configuración.
     */
    private function inicializarSelectores(
        SetupConfigService $config
    ): void {
        $this->provinciaId = null;
        $this->municipioId = null;
        $this->bancoId = null;
        $this->sucursal_id = null;

        $provinciaDefault = $config->get('provincia_default');

        if ($provinciaDefault) {
            $this->provinciaId = Provincia::query()
                ->activas()
                ->where('slug', $provinciaDefault)
                ->whereHas('municipios', function ($query) {
                    $this->aplicarMunicipiosDisponibles($query);
                })
                ->value('id');
        }

        $municipioDefault = $config->get('municipio_default');

        if ($this->provinciaId && $municipioDefault) {
            $this->municipioId = Municipio::query()
                ->where('provincia_id', $this->provinciaId)
                ->where('slug', $municipioDefault)
                ->where(function ($query) {
                    $this->aplicarMunicipiosDisponibles($query);
                })
                ->value('id');
        }

        $bancoDefault = $config->get('banco_default');

        if ($this->municipioId && $bancoDefault) {
            $this->bancoId = Banco::query()
                ->activos()
                ->where('municipio_id', $this->municipioId)
                ->where('codigo_swift', $bancoDefault)
                ->value('id');
        }

        $this->cargarMunicipios();
        $this->cargarBancos();
        $this->cargarSucursales();
    }

    /**
     * Aplica los criterios de disponibilidad de municipios.
     *
     * Un municipio debe estar activo y tener:
     * - algún banco activo, o
     * - alguna sucursal activa perteneciente a un banco activo.
     */

    private function aplicarMunicipiosDisponibles($query): void
    {
        $query->activos()
            ->where(function ($query) {
                $query->whereHas('bancos', function ($bancos) {
                    $bancos->activos();
                })->orWhereHas('sucursales', function ($sucursales) {
                    $sucursales
                        ->activas()
                        ->whereHas('banco', function ($banco) {
                            $banco->activos();
                        });
                });
            });
    }


    /**
     * Provincias activas con municipios disponibles.
     */
    private function provinciasDisponibles()
    {
        return Provincia::query()
            ->activas()
            ->whereHas('municipios', function ($query) {
                $this->aplicarMunicipiosDisponibles($query);
            })
            ->orderBy('nombre');
    }

    public function updated($property, $value): void
    {
        switch ($property) {
            case 'provinciaId':
                $this->municipioId = null;
                $this->bancoId = null;
                $this->sucursal_id = null;

                $this->cargarMunicipios();
                $this->cargarBancos();
                $this->cargarSucursales();

                $this->resetValidation([
                    'municipioId',
                    'sucursal_id',
                ]);

                $this->notificarFiltros();
                break;

            case 'municipioId':
                $this->bancoId = null;
                $this->sucursal_id = null;

                $this->cargarBancos();
                $this->cargarSucursales();

                $this->resetValidation('sucursal_id');

                $this->notificarFiltros();
                break;

            case 'bancoId':
                $this->sucursal_id = null;

                $this->cargarSucursales();

                $this->resetValidation('sucursal_id');

                $this->notificarFiltros();
                break;

            case 'sucursal_id':
            case 'moneda_id':
                $this->resetValidation($property);

                $this->notificarFiltros();
                break;
        }
    }

    public function aplicarFiltrosResultado(): void
    {
        $this->notificarFiltros();
    }

    private function notificarFiltros(): void
    {
        if (!$this->usar_filtros) {
            $this->dispatch('banco-limpiar-filtros');
        }

        $this->dispatch(
            'banco-filtros-actualizados',
            provinciaId: $this->usar_filtros
                ? $this->provinciaId
                : null,
            municipioId: $this->usar_filtros
                ? $this->municipioId
                : null,
            monedaId: $this->usar_filtros
                ? $this->moneda_id
                : null,
        );
    }

    private function cargarMunicipios(): void
    {
        if (!$this->provinciaId) {
            $this->municipios = [];
            return;
        }

        $query = Municipio::query()
            ->where('provincia_id', $this->provinciaId);

        $this->aplicarMunicipiosDisponibles($query);

        $this->municipios = $query
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->toArray();
    }

    private function cargarBancos(): void
    {
        if (!$this->municipioId) {
            $this->bancos = [];
            return;
        }

        $this->bancos = Banco::query()
            ->activos()
            ->where('municipio_id', $this->municipioId)
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->toArray();
    }

    private function cargarMonedas(): void
    {
        $this->monedas = app(MonedaService::class)
            ->listForDisplay();
    }

    private function cargarSucursales(): void
    {
        if (!$this->bancoId || !$this->municipioId) {
            $this->sucursales = [];
            return;
        }

        $this->sucursales = BancoSucursal::query()
            ->where('banco_id', $this->bancoId)
            ->where('municipio_id', $this->municipioId)
            ->where(function ($query) {
                $query->activas();

                // Permite conservar la sucursal de la cuenta
                // durante la edición aunque esté inactiva.
                if ($this->cuentaBancariaId && $this->sucursal_id) {
                    $query->orWhere(
                        'id',
                        $this->sucursal_id
                    );
                }
            })
            ->orderBy('nombre')
            ->get(['id', 'nombre'])
            ->toArray();
    }

    #[On('cuenta-bancaria-cargar-edicion')]
    public function edit(int $id): void
    {
        $cuentaBancaria = CuentaBancaria::query()
            ->with([
                'sucursal.banco',
                'sucursal.municipio.provincia',
            ])
            ->findOrFail($id);

        $sucursal = $cuentaBancaria->sucursal;
        $banco = $sucursal?->banco;
        $municipio = $sucursal?->municipio;
        $provincia = $municipio?->provincia;

        $this->cuentaBancariaId = $cuentaBancaria->id;

        $this->provinciaId = $provincia?->id;
        $this->municipioId = $municipio?->id;
        $this->bancoId = $banco?->id;

        $this->sucursal_id = $sucursal?->id;

        $this->moneda_id = (int) $cuentaBancaria->moneda_id;

        $this->numero_cuenta = $cuentaBancaria->numero_cuenta;
        $this->titular = $cuentaBancaria->titular;
        $this->descripcion = $cuentaBancaria->descripcion ?? '';
        $this->activa = (bool) $cuentaBancaria->activa;

        // Reconstruimos los catálogos después de establecer
        // todos los identificadores de la jerarquía.
        $this->cargarMunicipios();
        $this->cargarBancos();
        $this->cargarSucursales();

        $this->resetValidation();

        $this->dispatch(
            'cuenta-bancaria-edicion-cargado',
            cuentaBancariaId: $cuentaBancaria->id
        );
    }

    #[On('cuenta-bancaria-edicion-cargado')]
    public function bancoEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    public function save(): void
    {
        try {
            $this->validate();

            $payload = $this->payload();

            if ($this->cuentaBancariaId) {
                $this->command(
                    'cuentaBancaria.update',
                    $payload
                );

                $this->dispatch('cuenta-bancaria-actualizado');

                $this->dispatch('livewire:alert', [
                    'message' => 'Cuenta Bancaria actualizada correctamente.',
                    'type' => 'success',
                ]);
            } else {
                $this->command(
                    'cuentaBancaria.create',
                    $payload
                );

                $this->dispatch('cuenta-bancaria-creado');

                $this->dispatch('livewire:alert', [
                    'message' => 'Cuenta Bancaria creada correctamente.',
                    'type' => 'success',
                ]);
            }
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }

            return;
        } finally {
            $this->dispatch('loading-stop');
        }

        $this->dispatch('cuenta-bancaria-guardado');

        $this->resetForm();
    }

    private function payload(): array
    {
        return [
            'id' => $this->cuentaBancariaId,
            'sucursal_id' => $this->sucursal_id,
            'moneda_id' => $this->moneda_id,
            'numero_cuenta' => $this->numero_cuenta,
            'titular' => $this->titular,
            'descripcion' => $this->descripcion,
            'activa' => $this->activa,
        ];
    }

    public function cancel(): void
    {
        $this->resetForm();

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada.',
            'type' => 'warning',
        ]);
    }

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'cuentaBancariaId',
            'sucursal_id',
            'numero_cuenta',
            'titular',
            'descripcion',
            'activa',
        ]);

        $this->inicializarSelectores(
            app(SetupConfigService::class)
        );

        $monedaPrincipal = app(MonedaService::class)->principal();

        $this->moneda_id = $monedaPrincipal['id'] ?? null;

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.finanzas.cuenta-bancaria.form', [
            'provincias' => $this->provinciasDisponibles()->get([
                'id',
                'nombre',
            ]),
        ]);
    }
}
