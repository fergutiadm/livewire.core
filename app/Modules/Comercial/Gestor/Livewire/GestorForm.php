<?php

namespace App\Modules\Comercial\Gestor\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\Gestor;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class GestorForm extends Component
{
    use HasCommands;

    public ?int $gestorId = null;

    public ?int $userId = null;

    public string $nombreComercial = '';

    public string $descripcion = '';

    public bool $activo = true;

    public bool $editando = false;

    public function mount(): void
    {
        $this->resetForm();
    }

    protected function rules(): array
    {
        $rules = [
            'userId' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'nombreComercial' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],

            'activo' => [
                'boolean',
            ],
        ];

        if (!$this->editando) {
            $rules['userId'][] = Rule::unique('gestores', 'user_id');
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'userId.required' => 'El usuario es obligatorio.',
            'userId.integer' => 'El usuario seleccionado no es válido.',
            'userId.exists' => 'El usuario seleccionado no existe.',

            'nombreComercial.required' => 'El nombre comercial es obligatorio.',
            'nombreComercial.string' => 'El nombre comercial debe ser texto.',
            'nombreComercial.max' => 'El nombre comercial no puede superar los 255 caracteres.',

            'descripcion.string' => 'La descripción debe ser texto.',

            'activo.boolean' => 'El estado activo no es válido.',
        ];
    }

    #[On('gestor-cargar-edicion')]
    public function edit(int $id): void
    {
        $gestor = Gestor::findOrFail($id);

        $this->gestorId = $gestor->id;
        $this->userId = $gestor->user_id;
        $this->nombreComercial = $gestor->nombre_comercial;
        $this->descripcion = $gestor->descripcion ?? '';
        $this->activo = (bool) $gestor->activo;

        $this->editando = true;

        // Avisar que terminó la edición
        $this->dispatch(
            'gestor-edicion-cargado',
            gestorId: $gestor->id
        );

        $this->resetValidation();
    }

    #[On('gestor-edicion-cargado')]
    public function gestorEdicionCargada(): void
    {
        $this->dispatch('loading-stop');
    }

    public function save(): void
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('loading-stop');

            throw $e;
        }

        if ($this->editando) {
            $this->command(
                'gestor.update',
                [
                    'id' => $this->gestorId,
                    'nombre_comercial' => $this->nombreComercial,
                    'descripcion' => $this->descripcion !== ''
                        ? $this->descripcion
                        : null,
                    'activo' => $this->activo,
                ]
            );

            $this->dispatch('gestor-guardado');

            $this->dispatch('livewire:alert', [
                'message' => 'Gestor actualizado correctamente.',
                'type' => 'success',
            ]);
        } else {
            $this->command(
                'gestor.create',
                [
                    'user_id' => $this->userId,
                    'nombre_comercial' => $this->nombreComercial,
                    'descripcion' => $this->descripcion !== ''
                        ? $this->descripcion
                        : null,
                    'activo' => $this->activo,
                ]
            );

            $this->dispatch('gestor-guardado');

            $this->dispatch('loading-stop');

            $this->dispatch('livewire:alert', [
                'message' => 'Gestor creado correctamente.',
                'type' => 'success',
            ]);
        }

        $this->resetForm();
    }

    #[On('reset-form')]
    public function resetForm(): void
    {
        $this->gestorId = null;
        $this->userId = null;
        $this->nombreComercial = '';
        $this->descripcion = '';
        $this->activo = true;
        $this->editando = false;

        $this->resetValidation();
    }

    public function usuariosDisponibles()
    {
        $gestorUserIds = Gestor::withTrashed()
            ->pluck('user_id');

        return User::role('gestor')
            ->where(function ($query) use ($gestorUserIds) {
                $query->whereNotIn('id', $gestorUserIds);

                if ($this->editando && $this->userId) {
                    $query->orWhere('id', $this->userId);
                }
            })
            ->orderBy('name')
            ->get();
    }

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

    public function render()
    {
        return view('modules.comercial.gestor.form', [
            'usuarios' => $this->usuariosDisponibles(),
        ]);
    }
}
