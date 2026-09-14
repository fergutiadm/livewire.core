<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Local;

class ProductosPorCategoria extends Component
{
    public $local_id = null; // local seleccionado

    public function setLocal($id)
    {
        $this->local_id = $id;
    }

    public function render()
    {
        $locales = Local::orderBy('nombre')->get();
        $categorias = Categoria::with(['productos' => function($q) {
            if ($this->local_id) {
                $q->where('local_id', $this->local_id);
            }
            $q->orderBy('nombre');
        }])->get();

        return view('livewire.productos-por-categoria', [
            'locales' => $locales,
            'categorias' => $categorias,
        ]);
    }
}
