<?php

namespace App\Modules\Inventario\Atributo\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Atributo;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class AtributoForm extends Component
{
    use HasCommands;

    public ?int $atributoId = null;

    public string $codigo = '';

    public string $nombre = '';

    public string $descripcion = '';

    public int $orden_visual = 1;

    public array $valores = [];

    public ?int $valorEditandoIndex = null;

    public string $valorCodigo = '';

    public string $valorNombre = '';

    public bool $showDeleteValorModal = false;

    public ?int $valorEliminarIndex = null;

    protected function rules(): array
    {
        $codigoRule = Rule::unique(
            'atributos',
            'codigo'
        );

        if ($this->atributoId !== null) {
            $codigoRule->ignore($this->atributoId);
        }

        return [
            'codigo' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9._-]+$/',
                $codigoRule,
            ],

            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'min:3',
                'max:255',
            ],

            'valores.*.codigo' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9._-]+$/',
            ],

            'valores.*.nombre' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'codigo.required' =>
                'El código es obligatorio.',

            'codigo.max' =>
                'El código no puede superar 100 caracteres.',

            'codigo.regex' =>
                'El código solo puede contener letras, números, punto, guion y guion bajo.',

            'codigo.unique' =>
                'Ya existe un atributo con este código.',

            'nombre.required' =>
                'El Nombre es obligatorio.',

            'descripcion.min' =>
                'La descripción debe tener al menos 3 caracteres.',

            'descripcion.max' =>
                'Descripción demasiado larga.',

            'valores.*.codigo.required' =>
                'El código del valor es obligatorio.',

            'valores.*.codigo.regex' =>
                'El código del valor solo puede contener letras, números, punto, guion y guion bajo.',

            'valores.*.nombre.required' =>
                'El nombre del valor es obligatorio.',
        ];
    }

    #[On('atributo-nuevo')]
    public function nuevo(): void
    {
        $this->resetForm();
    }

    #[On('atributo-cargar-edicion')]
    public function edit(int $id): void
    {
        $atributo = Atributo::query()
            ->with([
                'valores' => function ($query) {
                    $query
                        // ->where('activo', true)
                        ->orderBy('orden_visual');
                },
            ])
            ->findOrFail($id);

        $this->atributoId = $atributo->id;
        $this->codigo = $atributo->codigo;
        $this->nombre = $atributo->nombre;
        $this->descripcion = $atributo->descripcion ?? '';

        $this->orden_visual = (int) (
            $atributo->orden_visual ?? 1
        );

        $this->valores = $atributo->valores
            ->map(function ($valor) {
                return [
                    'id' => $valor->id,

                    'key' => 'valor-' . $valor->id,

                    'codigo' => $valor->codigo,

                    'nombre' => $valor->nombre,

                    'orden_visual' => (int) (
                        $valor->orden_visual ?? 1
                    ),

                    'activo' => (bool)$valor->activo
                ];
            })
            ->values()
            ->toArray();

        $this->normalizarValores();

        $this->resetEditorValor();

        $this->resetValidation();

        $this->dispatch(
            'atributo-edicion-cargado',
            atributoId: $atributo->id
        );
    }

    public function agregarValor(): void
    {
        $this->valorCodigo = mb_strtoupper(
            trim($this->valorCodigo)
        );

        $this->valorNombre = trim(
            $this->valorNombre
        );

        $this->resetValidation([
            'valorCodigo',
            'valorNombre',
        ]);

        $this->validate([
            'valorCodigo' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Z0-9._-]+$/',
            ],

            'valorNombre' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'valorCodigo.required' =>
                'El código del valor es obligatorio.',

            'valorCodigo.regex' =>
                'El código del valor solo puede contener letras, números, punto, guion y guion bajo.',

            'valorNombre.required' =>
                'El nombre del valor es obligatorio.',
        ]);

        foreach ($this->valores as $index => $valor) {
            if (
                $this->valorEditandoIndex !== null
                && $index === $this->valorEditandoIndex
            ) {
                continue;
            }

            $codigoExistente = mb_strtoupper(
                trim((string) ($valor['codigo'] ?? ''))
            );

            if ($codigoExistente === $this->valorCodigo) {
                $this->addError(
                    'valorCodigo',
                    'Ya existe un valor con este código.'
                );

                return;
            }
        }

        if ($this->valorEditandoIndex !== null) {
            $index = $this->valorEditandoIndex;

            if (isset($this->valores[$index])) {
                $this->valores[$index]['codigo'] =
                    $this->valorCodigo;

                $this->valores[$index]['nombre'] =
                    $this->valorNombre;

                $this->normalizarValores();

                $this->resetEditorValor();

                return;
            }
        }

        $this->valores[] = [
            'id' => null,

            'key' => 'nuevo-' . Str::uuid()->toString(),

            'codigo' => $this->valorCodigo,

            'nombre' => $this->valorNombre,

            'orden_visual' => count($this->valores) + 1,
        ];

        $this->normalizarValores();

        $this->resetEditorValor();
    }

    public function editarValor(int $index): void
    {
        if (!isset($this->valores[$index])) {
            return;
        }

        $valor = $this->valores[$index];

        $this->valorEditandoIndex = $index;

        $this->valorCodigo = (string) (
            $valor['codigo'] ?? ''
        );

        $this->valorNombre = (string) (
            $valor['nombre'] ?? ''
        );

        $this->resetValidation([
            'valorCodigo',
            'valorNombre',
        ]);
    }

    public function eliminarValor(int $index): void
    {
        if (!isset($this->valores[$index])) {
            return;
        }

        unset($this->valores[$index]);

        $this->valores = array_values(
            $this->valores
        );

        $this->normalizarValores();

        $this->resetEditorValor();
    }

    public function reordenarValores(array $orderedItems): void
    {
        $ordenClaves = [];

        foreach ($orderedItems as $item) {
            /*
            * Formato habitual de wire:sortable:
            *
            * [
            *     'order' => 1,
            *     'value' => 'valor-15',
            * ]
            */
            if (is_array($item)) {
                if (isset($item['value'])) {
                    $ordenClaves[] = (string) $item['value'];

                    continue;
                }

                if (isset($item['id'])) {
                    $ordenClaves[] = (string) $item['id'];

                    continue;
                }
            }

            /*
            * Por compatibilidad con una lista simple:
            *
            * [
            *     'valor-15',
            *     'valor-16',
            * ]
            */
            if (is_scalar($item)) {
                $ordenClaves[] = (string) $item;
            }
        }

        if ($ordenClaves === []) {
            return;
        }

        $valoresPorClave = [];

        foreach ($this->valores as $index => $valor) {
            $key = $valor['key'] ?? null;

            if ($key === null) {
                $key = !empty($valor['id'])
                    ? 'valor-' . $valor['id']
                    : 'item-' . $index;

                $this->valores[$index]['key'] = $key;
            }

            $valoresPorClave[(string) $key] =
                $this->valores[$index];
        }

        /*
        * Guardamos la clave del valor actualmente editándose.
        */
        $valorEditandoKey = null;

        if ($this->valorEditandoIndex !== null) {
            $valorEditandoKey =
                $this->valores[$this->valorEditandoIndex]['key']
                ?? null;
        }

        $reordenados = [];

        foreach ($ordenClaves as $key) {
            if (isset($valoresPorClave[$key])) {
                $reordenados[] =
                    $valoresPorClave[$key];

                unset($valoresPorClave[$key]);
            }
        }

        /*
        * Cualquier elemento no recibido por el evento se conserva.
        */
        foreach ($valoresPorClave as $valor) {
            $reordenados[] = $valor;
        }

        $this->valores = array_values(
            $reordenados
        );

        $this->normalizarValores();

        /*
        * Mantener la edición sobre el mismo valor después
        * del reordenamiento.
        */
        $this->valorEditandoIndex = null;

        if ($valorEditandoKey !== null) {
            foreach ($this->valores as $index => $valor) {
                if (
                    ($valor['key'] ?? null)
                    === $valorEditandoKey
                ) {
                    $this->valorEditandoIndex = $index;

                    break;
                }
            }
        }
    }

    public function cancelarEdicionValor(): void
    {
        $this->resetEditorValor();
    }

    #[On('atributo-edicion-cargado')]
    public function atributoEdicionCargado(): void
    {
        $this->dispatch('loading-stop');
    }

    public function save(): void
    {
        try {
            $this->codigo = mb_strtoupper(
                trim($this->codigo)
            );

            $this->nombre = trim(
                $this->nombre
            );

            $this->descripcion = trim(
                $this->descripcion
            );

            $this->normalizarValores();

            $this->validate();

            $this->validarCodigosValores();
        } catch (ValidationException $e) {
            $this->dispatch('loading-stop');

            throw $e;
        }

        $payload = $this->payload();

        if ($this->atributoId !== null) {
            $payload['id'] = $this->atributoId;

            $this->command(
                'atributo.update',
                $payload
            );

            $this->dispatch(
                'atributo-actualizado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Atributo actualizado correctamente...',
                    'type' => 'success',
                ]
            );
        } else {
            $this->command(
                'atributo.create',
                $payload
            );

            $this->dispatch(
                'atributo-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Atributo creado correctamente...',
                    'type' => 'success',
                ]
            );
        }

        $this->dispatch(
            'atributo-guardado'
        );

        $this->dispatch('loading-stop');

        $this->resetForm();
    }

    private function validarCodigosValores(): void
    {
        $codigos = [];

        foreach ($this->valores as $index => $valor) {
            $codigo = mb_strtoupper(
                trim((string) ($valor['codigo'] ?? ''))
            );

            if (isset($codigos[$codigo])) {
                throw ValidationException::withMessages([
                    "valores.{$index}.codigo" =>
                        'Este código ya está siendo utilizado.',
                ]);
            }

            $codigos[$codigo] = true;
        }
    }

    private function payload(): array
    {
        return [
            'id' => $this->atributoId,

            'codigo' => $this->codigo,

            'nombre' => $this->nombre,

            'descripcion' =>
                $this->descripcion !== ''
                    ? $this->descripcion
                    : null,

            'orden_visual' => $this->orden_visual,

            'valores' => array_map(
                static function (array $valor): array {
                    return [
                        'id' => $valor['id'] ?? null,

                        'codigo' => $valor['codigo'] ?? '',

                        'nombre' => $valor['nombre'] ?? '',

                        'orden_visual' =>
                            $valor['orden_visual'] ?? null,

                        'activo' => $valor['activo'] ?? true,
                    ];
                },
                $this->valores
            ),
        ];
    }

    public function cancel(): void
    {
        $this->resetForm();

        $this->dispatch(
            'livewire:alert',
            [
                'message' => 'Acción cancelada...',
                'type' => 'warning',
            ]
        );
    }

    #[On('reset-form')]
    public function resetFormEvent(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->atributoId = null;

        $this->codigo = '';

        $this->nombre = '';

        $this->descripcion = '';

        $this->orden_visual = 1;

        $this->valores = [];

        $this->showDeleteValorModal = false;

        $this->valorEliminarIndex = null;

        $this->resetEditorValor();

        $this->resetValidation();
    }

    private function resetEditorValor(): void
    {
        $this->valorEditandoIndex = null;

        $this->valorCodigo = '';

        $this->valorNombre = '';

        $this->resetValidation([
            'valorCodigo',
            'valorNombre',
        ]);
    }

    private function normalizarValores(): void
    {
        foreach ($this->valores as $index => $valor) {
            if (empty($valor['key'])) {
                $this->valores[$index]['key'] =
                    !empty($valor['id'])
                        ? 'valor-' . $valor['id']
                        : 'item-' . $index;
            }

            $this->valores[$index]['orden_visual'] =
                $index + 1;

            $this->valores[$index]['codigo'] =
                mb_strtoupper(
                    trim((string) (
                        $valor['codigo'] ?? ''
                    ))
                );

            $this->valores[$index]['nombre'] =
                trim((string) (
                    $valor['nombre'] ?? ''
                ));
        }

        $this->valores = array_values(
            $this->valores
        );
    }

    public function toggleValorActivo(int $index): void
    {
        if (!isset($this->valores[$index])) {
            return;
        }

        $this->valores[$index]['activo'] =
            !($this->valores[$index]['activo'] ?? true);
    }

    public function solicitarEliminacionValor(int $index): void
    {
        if (!isset($this->valores[$index])) {
            return;
        }

        $this->valorEliminarIndex = $index;
        $this->showDeleteValorModal = true;
    }

    public function cancelarEliminacion(): void
    {
        $this->showDeleteValorModal = false;
        $this->valorEliminarIndex = null;

        $this->dispatch('livewire:alert', [
            'message' => 'Acción cancelada...',
            'type' => 'warning',
        ]);
    }

    public function confirmarEliminacion(): void
    {
        // $this->dispatch(
        //     'loading-start',
        //     message: 'Eliminando valor...'
        // );

        try {
            if (
                $this->valorEliminarIndex === null
                || !isset($this->valores[$this->valorEliminarIndex])
            ) {
                return;
            }

            $indexEliminar = $this->valorEliminarIndex;

            unset($this->valores[$indexEliminar]);

            $this->valores = array_values(
                $this->valores
            );

            $this->normalizarValores();

            if (
                $this->valorEditandoIndex !== null
                && $this->valorEditandoIndex === $indexEliminar
            ) {
                $this->resetEditorValor();
            } elseif (
                $this->valorEditandoIndex !== null
                && $this->valorEditandoIndex > $indexEliminar
            ) {
                $this->valorEditandoIndex--;
            }

            $this->showDeleteValorModal = false;
            $this->valorEliminarIndex = null;

            $this->dispatch('livewire:alert', [
                'message' => 'Valor retirado correctamente...',
                'type' => 'success',
            ]);
        } finally {
            $this->dispatch('loading-stop');
        }
    }

    public function render()
    {
        return view(
            'modules.inventario.atributo.form'
        );
    }
}