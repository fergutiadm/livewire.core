<?php

namespace App\Modules\Seguridad\User\Livewire;

use App\Core\CQRS\HasCommands;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class UserForm extends Component
{
    use HasCommands;

    public ?int $userId = null;

    public string $name = '';
    public string $email = '';
    public string $movil = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = '';

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->userId),
            ],

            'movil' => [
                'required',
            ],

            'role' => [
                'required',
                'exists:roles,name',
            ],

            'password' => [
                $this->userId ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'       => 'El nombre del usuario es obligatorio.',
            'name.max'            => 'El nombre es demasiado largo (máximo 255 caracteres).',
            'email.required'      => 'El correo electrónico es obligatorio.',
            'email.email'         => 'Ingrese un formato de correo electrónico válido.',
            'email.unique'        => 'Este correo electrónico ya está registrado en el sistema.',
            'movil.required'      => 'El móvil es obligatorio.',
            'role.required'       => 'Debe asignar un rol al usuario.',
            'role.exists'         => 'El rol seleccionado no es válido.',
            'password.required'   => 'La contraseña es obligatoria para nuevos usuarios.',
            'password.min'        => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'  => 'Las contraseñas ingresadas no coinciden.',
        ];
    }

    public function agregarArroba(): void
    {
        $email = trim($this->email);

        if ($email === '') {
            return;
        }

        if (str_contains($email, '@')) {
            return;
        }

        $this->email .= '@';
    }

    #[On('user-cargar-edicion')]
    public function edit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;

        $this->name = $user->name;

        $this->email = $user->email ?? '';

        $this->movil = $user->movil ?? '';

        $this->role = $user->getRoleNames()->first() ?? '';

        $this->password = '';

        $this->password_confirmation = '';


        // Avisar al MediaManager qué categoría se está editando
        // $this->dispatch(
        //     'user-media-cargar',
        //     userId: $user->id
        // );

        // Avisar que terminó la edición
        $this->dispatch(
            'user-edicion-cargado',
            userId: $user->id
        );

        $this->resetValidation();
    }

    #[On('user-edicion-cargado')]
    public function userEdicionCargada(): void
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

        if ($this->userId) {

            $payload = $this->payload();

            //Logger()->info("ACTUALIZANDO PRODUCTO", ['payload'=>$payload]);

            $user = $this->command(
                'user.update',
                $payload,
            );

            $this->dispatch(
                'user-actualizado',
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Ususario Actualizado correctamente...',
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

            $user = $this->command(
                'user.create',
                $this->payload()
            );

            $this->dispatch(
                'user-creado'
            );

            $this->dispatch(
                'livewire:alert',
                [
                    'message' =>
                        'Ususario Creado correctamente...',
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
            'user-guardado'
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
            'id' => $this->userId,

            'user_id' => $this->userId,

            'name' => $this->name,

            'email' => $this->email,

            'movil' => $this->movil,

            'password' => $this->password !== ''
            ? $this->password
            : null,

            'role' => $this->role,
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
        Log::info('Listener reset-form', [
            'userId' => $this->userId,
        ]);

        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'userId',
            'name',
            'email',
            'movil',
            'password',
            'password_confirmation',
            'role',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.seguridad.user.form', [
            'roles' => Role::all()
        ]);
    }
}

