<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Categoria;

class ProductosGrid extends Component
{
    public $categoria_id = null; // filtro

    public function setCategoria($id)
    {
        $this->categoria_id = $id;
    }

    public function render()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        $productosQuery = Producto::query()->orderBy('nombre');

        if ($this->categoria_id) {
            $productosQuery->where('categoria_id', $this->categoria_id);
        }

        $productos = $productosQuery->get();

        return view('livewire.productos-grid', [
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }
}
