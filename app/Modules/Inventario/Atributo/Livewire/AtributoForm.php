<?php

namespace App\Modules\Inventario\Atributo\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Atributo;
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
                'El código del valor contiene caracteres no permitidos.',

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
                        ->where('activo', true)
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
                    'codigo' => $valor->codigo,
                    'nombre' => $valor->nombre,
                    'orden_visual' => (int) (
                        $valor->orden_visual ?? 1
                    ),
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
                'El código del valor contiene caracteres no permitidos.',

            'valorNombre.required' =>
                'El nombre del valor es obligatorio.',
        ]);

        $codigo = mb_strtoupper(
            trim($this->valorCodigo)
        );

        $nombre = trim(
            $this->valorNombre
        );

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

            if ($codigoExistente === $codigo) {
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
                $this->valores[$index]['codigo'] = $codigo;
                $this->valores[$index]['nombre'] = $nombre;

                $this->normalizarValores();
                $this->resetEditorValor();

                return;
            }
        }

        $this->valores[] = [
            'id' => null,
            'codigo' => $codigo,
            'nombre' => $nombre,
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

    public function moverValorArriba(int $index): void
    {
        if (
            $index <= 0
            || !isset($this->valores[$index])
        ) {
            return;
        }

        $anterior = $index - 1;

        [
            $this->valores[$anterior],
            $this->valores[$index],
        ] = [
            $this->valores[$index],
            $this->valores[$anterior],
        ];

        $this->normalizarValores();

        if ($this->valorEditandoIndex === $index) {
            $this->valorEditandoIndex = $anterior;
        } elseif ($this->valorEditandoIndex === $anterior) {
            $this->valorEditandoIndex = $index;
        }
    }

    public function moverValorAbajo(int $index): void
    {
        $ultimo = count($this->valores) - 1;

        if (
            $index < 0
            || $index >= $ultimo
            || !isset($this->valores[$index])
        ) {
            return;
        }

        $siguiente = $index + 1;

        [
            $this->valores[$index],
            $this->valores[$siguiente],
        ] = [
            $this->valores[$siguiente],
            $this->valores[$index],
        ];

        $this->normalizarValores();

        if ($this->valorEditandoIndex === $index) {
            $this->valorEditandoIndex = $siguiente;
        } elseif ($this->valorEditandoIndex === $siguiente) {
            $this->valorEditandoIndex = $index;
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

            'codigo' => mb_strtoupper(
                trim($this->codigo)
            ),

            'nombre' => trim(
                $this->nombre
            ),

            'descripcion' => trim(
                $this->descripcion
            ) !== ''
                ? trim($this->descripcion)
                : null,

            'orden_visual' => $this->orden_visual,

            'valores' => $this->valores,
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

    public function render()
    {
        return view(
            'modules.inventario.atributo.form'
        );
    }
}