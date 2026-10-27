<?php

namespace App\Modules\Configuracion\SetupConfig\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\SetupConfig;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class SetupConfigForm extends Component
{
    use HasCommands;

    public ?int $setupConfigId = null;

    public string $nombre = '';
    public string $item_id = '';
    public string $descripcion = '';
    public int $tipo = SetupConfig::TIPO_NUMERO;
    public string $valor = '';
    public string $valor_inicial = '';
    public string $valor_minimo = '';
    public string $valor_maximo = '';

    public array $tipos = [
        ['id' => 1, 'nombre' => 'Numérico'],
        ['id' => 2, 'nombre' => 'Texto'],
        ['id' => 3, 'nombre' => 'Arreglo'],
    ];

    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'item_id' => [
                'required',
                'string',
                'max:150',
                Rule::unique('setup_configs', 'item_id')
                    ->ignore($this->setupConfigId),
            ],
            'descripcion' => ['nullable', 'string'],
            'tipo' => [
                'required',
                'integer',
                Rule::in([
                    SetupConfig::TIPO_NUMERO,
                    SetupConfig::TIPO_TEXTO,
                    SetupConfig::TIPO_ARREGLO,
                ]),
            ],
            'valor' => ['required', 'string'],
            'valor_inicial' => ['nullable', 'string'],
            'valor_minimo' => ['nullable', 'string'],
            'valor_maximo' => ['nullable', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar 150 caracteres.',
            'item_id.required' => 'El identificador es obligatorio.',
            'item_id.unique' => 'Ya existe una configuración con ese identificador.',
            'tipo.in' => 'El tipo seleccionado no es válido.',
            'valor.required' => 'El valor actual es obligatorio.',
        ];
    }

    #[On('setup-config-cargar-edicion')]
    public function cargarEdicion(int $id): void
    {
        $config = SetupConfig::findOrFail($id);

        $this->setupConfigId = $config->id;
        $this->nombre = $config->nombre;
        $this->item_id = $config->item_id;
        $this->descripcion = $config->descripcion ?? '';
        $this->tipo = (int) $config->tipo;
        $this->valor = $config->valor;
        $this->valor_inicial = $config->valor_inicial ?? '';
        $this->valor_minimo = $config->valor_minimo ?? '';
        $this->valor_maximo = $config->valor_maximo ?? '';

        $this->resetValidation();

        $this->dispatch(
            'setup-config-edicion-cargado',
            setupConfigId: $config->id
        );
    }

    public function guardar(): void
    {
        $data = $this->validate();

        $payload = [
            'id' => $this->setupConfigId,
            'nombre' => trim($data['nombre']),
            'item_id' => trim($data['item_id']),
            'descripcion' => $data['descripcion'] ?: null,
            'tipo' => (int) $data['tipo'],
            'valor' => $data['valor'],
            'valor_inicial' => $data['valor_inicial'] ?? '',
            'valor_minimo' => $data['valor_minimo'] ?? '',
            'valor_maximo' => $data['valor_maximo'] ?? '',
        ];

        if ($this->setupConfigId !== null) {
            $this->command('setupConfig.update', $payload);

            $mensaje = 'Configuración actualizada correctamente.';
        } else {
            unset($payload['id']);

            $this->command('setupConfig.create', $payload);

            $mensaje = 'Configuración creada correctamente.';
        }

        $this->dispatch('setup-config-guardado');
        $this->dispatch('loading-stop');
        $this->dispatch('alert', [
            'type' => 'success',
            'message' => $mensaje,
        ]);

        $this->resetForm();
    }

    public function cancelar(): void
    {
        $this->resetForm();

        $this->dispatch('alert', [
            'type' => 'warning',
            'message' => 'Edición cancelada.',
        ]);
    }

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'setupConfigId',
            'nombre',
            'item_id',
            'descripcion',
            'valor',
            'valor_inicial',
            'valor_minimo',
            'valor_maximo',
        ]);

        $this->tipo = SetupConfig::TIPO_NUMERO;

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.configuracion.setup-config.form');
    }
}
