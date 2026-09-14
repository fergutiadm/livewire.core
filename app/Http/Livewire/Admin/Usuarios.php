<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class Usuarios extends Component
{
    use WithPagination;

    // Propiedades del modelo
    public $name, $email, $movil, $password, $password_confirmation, $role, $userId;

    // Control de UI
    public $perPage = 10;
    public $formularioVisible = true;
    public ?int $userIdToDelete = null;
    public bool $confirmingUserDeletion = false;

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function save()
    {
        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'movil' => 'required',
            'role'  => 'required|exists:roles,name',
        ];

        // Si es nuevo: Obligatoria. Si es edición: Solo si se rellena.
        if (!$this->userId) {
            $rules['password'] = 'required|min:8|confirmed';
        } elseif ($this->password) {
            $rules['password'] = 'min:8|confirmed';
        }

        $this->validate($rules);

        try {
            if ($this->userId) {
                $user = User::findOrFail($this->userId);
                $user->name = $this->name;
                $user->email = $this->email;

                // 🔐 Solo actualiza el hash si se escribió una nueva
                if ($this->password) {
                    $user->password = Hash::make($this->password);
                }

                $user->save();
                $user->syncRoles($this->role);
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                ]);
                $user->assignRole($this->role);
            }

            $this->dispatch('livewire:confirm', [
                'message'  => $this->userId ? 'Usuario actualizado con éxito.' : 'Usuario creado con éxito',
                'type'     => 'success',
            ]);

            $this->resetForm();
            $this->dispatch('usuariosActualizados'); // Para refrescar la tabla lazy

        } catch (Throwable $e) {
            Log::error('Error en save usuarios', ['exception' => $e]);
            $this->dispatch('livewire:alert', ['message' => 'Error al procesar usuario.', 'type' => 'error']);
        }
    }

    protected function messages()
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

    #[On('edit')]
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            $this->userId   = $user->id;
            $this->name     = $user->name;
            $this->email    = $user->email;
            $this->movil    = $user->movil;
            $this->role     = $user->getRoleNames()->first() ?? '';
            $this->formularioVisible = true;
        } catch (Throwable $e) {
            $this->dispatch('livewire:alert', ['message' => 'Usuario no encontrado.', 'type' => 'error']);
        }
    }

    #[On('confirmDelete')]
    public function confirmDelete(int $id)
    {
        $this->userIdToDelete = $id;
        $this->confirmingUserDeletion = true;
    }

    public function delete()
    {
        if (!$this->userIdToDelete) return;

        try {
            $user = User::findOrFail($this->userIdToDelete);
            $user->delete();

            $this->confirmingUserDeletion = false;
            $this->dispatch('usuariosActualizados');
            $this->dispatch('livewire:confirm', ['message' => 'Usuario eliminado.', 'type' => 'success']);
        } catch (Throwable $e) {
            Log::error('Error delete usuario', ['id' => $this->userIdToDelete, 'exception' => $e]);
        }

        $this->userIdToDelete = null;
    }

    public function cancelDelete()
    {
        $this->userIdToDelete = null;
        $this->confirmingUserDeletion = false;
    }

    public function cancel() {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['userId', 'name', 'email', 'movil', 'password', 'password_confirmation', 'role']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.usuarios', [
            'roles' => Role::all()
        ])->layout('layouts.app_admin');
    }
}
