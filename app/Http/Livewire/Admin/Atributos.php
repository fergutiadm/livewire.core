<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Atributo;
use App\Models\AtributoValor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Throwable;

class Atributos extends Component
{
    use WithPagination;

    // ATRIBUTO
    public $atributoId;
    public $nombre;
    public $descripcion;
    public $orden_visual;

    // VALORES
    public $valorId;
    public $valor;
    public $valorDescripcion;
    public $valores = [];

    // Estado
    public $showValores = false;
    public $atributoIdToDelete = null;
    public bool $confirmingAtributoDeletion = false;
    public $valorIdToDelete = null;

    public $perPage = 10;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'valor' => 'required|string|max:255',
    ];

    public $formularioVisible = true;

    /* =========================
     *  ATRIBUTOS
     * ========================= */

    public function updatingPerPage() { $this->resetPage(); }

    public function save()
    {
        $this->validateOnly('nombre');

        if (!$this->atributoId) {
            $this->orden_visual = (Atributo::max('orden_visual') ?? 0) + 1;
        }

        $data = [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'orden_visual' => $this->orden_visual,
        ];

        if ($this->atributoId) {
            $atributo = Atributo::findOrFail($this->atributoId);
            $atributo->fill($data);
            $atributo->save(); // dispara observer → traza registrada
        } else {
            $atributo = Atributo::create($data);
            $this->atributoId = $atributo->id;
        }

        $this->dispatch('livewire:confirm', [
            'message'  => $this->localId ? 'Sub Categoría actualizado con éxito.' : 'Sub Categoría creado con éxito',
            'type'     => 'success',
        ]);

        usleep(500000);
        $this->dispatch('atributosActualizados');

        $this->resetForm();
        $this->dispatch('AtributosActualizados');
    }

    #[On('edit')]
    public function edit($id)
    {
        try{
            $atributo = Atributo::findOrFail($id);
            $this->atributoId = $atributo->id;
            $this->nombre = $atributo->nombre;
            $this->descripcion = $atributo->descripcion;
            $this->orden_visual = $atributo->orden_visual;

            $this->showValores = true;
            $this->loadValores();
            $this->resetValoresForm();
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método edit atributo', [
                    'atributo_id' => $id,
                    'exception'   => $e,
                ]);
            return;
        }
    }

    public function cancel()
    {
        $this->resetForm();
        $this->showValores = false;
        $this->valores = [];
    }

    public function cancelDelete() {
        $this->atributoIdToDelete = null;
    }

    #[On('confirmDelete')]
    public function confirmDelete($id) {
        $this->atributoIdToDelete = $id;
        $this->confirmingAtributoDeletion = true;
    }

    public function delete()
    {
        if (!$this->atributoIdToDelete)
            return;

        try{
            Atributo::findOrFail($this->atributoIdToDelete)->delete();
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método delete atributos', [
                    'atributo_id' => $this->atributoIdToDelete,
                    'exception'   => $e,
                ]);
            return;
        }


        $this->confirmingAtributoDeletion = false;

        usleep(500000);
        $this->dispatch('atributosActualizados');

        $this->resetForm();
        $this->atributoIdToDelete = null;
    }

    /* =========================
     *  DRAG & DROP ATRIBUTOS
     * ========================= */
    public function reorderAtributos($items)
    {
        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                Atributo::where('id', $item['value'])
                    ->update(['orden_visual' => $item['order']]);
            }
        });

        // 🔴 CLAVE: recargar la página actual
        $this->resetPage();
    }
    // public function reorderAtributos($items)
    // {
    //     // Traer los atributos visibles actualmente
    //     $atributosPage = Atributo::orderBy('orden_visual')->get();

    //     DB::transaction(function() use ($items, $atributosPage) {
    //         // $items es un array de IDs en el nuevo orden
    //         foreach ($items as $index => $id) {
    //             $atributo = $atributosPage->where('id', $id)->first();
    //             if ($atributo) {
    //                 $atributo->orden_visual = $index + 1; // nuevo orden
    //                 $atributo->save();
    //             }
    //         }
    //     });

    //     $this->resetPage(); // recarga la página para ver los cambios
    // }

    /* =========================
     *  VALORES
     * ========================= */
    public function loadValores()
    {
        if (!$this->atributoId) return;

        $this->valores = AtributoValor::where('atributo_id', $this->atributoId)
            ->orderBy('orden_visual')
            ->get()
            ->toArray();
    }

    public function editValor($id)
    {
        try{
            $valor = AtributoValor::where('atributo_id', $this->atributoId)->findOrFail($id);
        }catch(Throwable $e){
            $this->dispatch('livewire:alert',[
                            'message' => 'Ha ocurrido un error. Contacte al administrador del sistema.',
                            'type' => 'error']
                            );
            Log::error('Error en método edit AtributoValor', [
                    'atributo_id'       => $this->atributoId,
                    'atributo_valor_id' => $id,
                    'exception'         => $e,
                ]);
            return;
        }

        $this->valorId = $valor->id;
        $this->valor = $valor->valor;
        $this->valorDescripcion = $valor->descripcion;
    }

    public function saveValor()
    {
        $this->validateOnly('valor');

        $data = [
            'atributo_id' => $this->atributoId,
            'valor' => $this->valor,
            'descripcion' => $this->valorDescripcion,
        ];

        if ($this->valorId) {
            $valor = AtributoValor::findOrFail($this->valorId);
            $valor->fill($data);
            $valor->save(); // dispara observer → traza registrada
        } else {
            $data['orden_visual'] = (AtributoValor::where('atributo_id', $this->atributoId)->max('orden_visual') ?? 0) + 1;
            AtributoValor::create($data);
        }

        $this->resetValoresForm();
        $this->loadValores();
    }

    public function confirmDeleteValor($id) { $this->valorIdToDelete = $id; }

    public function deleteValor($id)
    {
        $valor = AtributoValor::findOrFail($id);
        if ($valor->atributo_id !== $this->atributoId) return;

        $valor->delete();
        $this->reordenarValoresBlindaje($this->atributoId);
        $this->resetValoresForm();
        $this->loadValores();
    }

    // public function reorderValores($items)
    // {
    //     if (!$this->atributoId) return;

    //     $valores = AtributoValor::where('atributo_id', $this->atributoId)
    //                 ->orderBy('orden_visual')
    //                 ->get();

    //     DB::transaction(function() use ($items, $valores) {
    //         foreach ($items as $index => $id) {
    //             $valor = $valores->where('id', $id)->first();
    //             if ($valor) {
    //                 $valor->orden_visual = $index + 1;
    //                 $valor->save();
    //             }
    //         }
    //     });

    //     $this->loadValores(); // recarga la lista de valores
    // }

    public function reorderValores($items)
    {
        if (!$this->atributoId) return;

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                AtributoValor::where('id', $item['value'])
                    ->where('atributo_id', $this->atributoId)
                    ->update(['orden_visual' => $item['order']]);
            }
        });

        $this->loadValores();
    }

    private function reordenarValoresBlindaje(int $atributoId)
    {
        $valores = AtributoValor::where('atributo_id', $atributoId)
                    ->orderBy('orden_visual')
                    ->get();

        $orden = 1;
        foreach ($valores as $valor) {
            $valor->orden_visual = $orden++;
            $valor->save();
        }
    }

    /* =========================
     *  HELPERS
     * ========================= */
    private function resetForm()
    {
        $this->reset(['atributoId', 'nombre', 'descripcion', 'orden_visual', 'atributoIdToDelete']);
        $this->resetValoresForm();
        $this->resetValidation();
    }

    private function resetValoresForm()
    {
        $this->reset(['valorId', 'valor', 'valorDescripcion', 'valorIdToDelete']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.atributos')
            ->layout('layouts.app_admin');
    }
}
