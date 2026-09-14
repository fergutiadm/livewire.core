<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TablaUsuarios extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';
    public $roleFilter = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingRoleFilter() { $this->resetPage(); }

    // Soporte para lazy loading
    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando lista de usuarios...'
        ]);
    }

    public function edit($id)
    {
        $this->dispatch('edit', id: $id)->to(Usuarios::class);
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirmDelete', id: $id)->to(Usuarios::class);
    }

    #[On('usuariosActualizados')]
    public function recargarUsuarios()
    {
        usleep(500000);
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query()
            ->with('roles')
            ->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('movil', 'like', "%{$this->search}%");
            });

        if ($this->roleFilter) {
            $query->role($this->roleFilter); // Scope de Spatie
        }

        return view('livewire.admin.tabla-usuarios', [
            'usuarios' => $query->orderBy('name')->paginate($this->perPage),
            'roles' => Role::all()
        ]);
    }
}
