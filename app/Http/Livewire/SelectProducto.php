<?php

namespace App\Http\Livewire;

use App\Models\Categoria;
use App\Models\Producto;
use Livewire\Component;

class SelectProducto extends Component
{
    public int $localId;

    public $categorias;
    public $productos = [];

    public $categoriaId = null;
    public $productoId = null;

    public function mount(int $localId, $categoriaId = null, $productoId = null)
    {
        $this->localId = $localId;
        $this->categoriaId = $categoriaId;
        $this->productoId = $productoId;

        $this->categorias = Categoria::where(function ($q) {
                $q->whereNull('local_id')
                  ->orWhere('local_id', $this->localId);
            })
            ->orderBy('nombre')
            ->get();

        $this->productos = collect();
    }

    public function cargarProductos()
    {
        logger('cargarProductos disparado', ['categoriaId' => $this->categoriaId]);

        if (!$this->categoriaId) {
            $this->productos = [];
            $this->productoId = null;
            return;
        }

        $this->productos = Producto::where('categoria_id', $this->categoriaId)
            ->orderBy('nombre')
            ->get();

        $this->productoId = $this->productos->first()->id ?? null;
    }

    public function render()
    {
        return view('livewire.select-producto');
    }
}
