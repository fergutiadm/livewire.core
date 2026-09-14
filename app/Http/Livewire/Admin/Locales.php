<?php

namespace App\Http\Livewire\Admin;

use App\Models\Local;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

use Livewire\WithPagination;
use Throwable;

class Locales extends Component
{
    use WithPagination;

    public $nombre;
    public $descripcion;
    //public $locales = [];
    public $localId;

    public ?int $localIdToDelete = null;
    public bool $confirmingLocalDeletion = false;


    public $localEdit = [
    'nombre' => '',
    'descripcion' => ''
    ];

    public $perPage = 10; // items por página
    public $formularioVisible = true;

    public function updatingPerPage()
    {
        $this->resetPage(); // resetea la página al cambiar perPage
    }

    public function mount()
    {
        // $this->loadLocales();
    }

    // public function loadLocales()
    // {
    //     $this->locales = Local::orderBy('nombre')->get();
    // }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ],
        [
            'nombre.required' => 'El Nombre es obligatorio',
        ]);

        if ($this->localId) {
            // ✏️ Actualizar
            $local = Local::findOrFail($this->localId);
            $local->nombre = $this->nombre;
            $local->descripcion = $this->descripcion;
            $local->save(); // dispara observer → traza registrada
        } else {
            // ➕ Crear
            Local::create([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
            ]);
        }

        $this->dispatch('livewire:confirm', [
            'message'  => $this->localId ? 'Local actualizado con éxito.' : 'Local creado con éxito',
            'type'     => 'success',
        ]);

        $this->resetForm();
        // $this->loadLocales();
        $this->dispatch('localesActualizados');
    }

    #[On('confirmDelete')]
    public function confirmDelete(int $id)
    {
        $this->localIdToDelete = $id;
        $this->confirmingLocalDeletion = true;
    }

    public function cancelDelete()
    {
        $this->localIdToDelete = null;
    }

    public function delete()
    {
        if (!$this->localIdToDelete) return;

        try{
            $local = Local::findOrFail($this->localIdToDelete);

            $local->delete();
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método delete locales', [
                    'local_id' => $this->localIdToDelete,
                    'exception'   => $e,
                ]);
            return;
        }

        $this->confirmingLocalDeletion = false;
        usleep(500000);
        $this->dispatch('localesActualizadas');

        $this->localIdToDelete = null;

        $this->resetForm();
        // $this->loadLocales();
    }

    #[On('edit')]
    public function edit($id)
    {
        try{
            $local = Local::findOrFail($id);
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método edit local', [
                    'local_id'    => $id,
                    'exception'   => $e,
                ]);
            return;
        }

        $this->localId = $local->id;
        $this->nombre = $local->nombre;
        $this->descripcion = $local->descripcion;
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset('localId', 'nombre', 'descripcion');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.locales')
                ->layout('layouts.app_admin');
    }
}
