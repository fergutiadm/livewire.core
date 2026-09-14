<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Local;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class ProductosPorCategoriaFinal extends Component
{
    use WithPagination;

    public $localId = null;
    public $categoriaId = null;
    public $perPage = 5;
    public $search = '';

    // Seleccionar local desde botones
    public function setLocal($id = null)
    {
        $this->localId = $id;
        $this->categoriaId = null;
        $this->setPage(1);
    }

    // Seleccionar categoria desde botones
    public function setCategoria($id = null)
    {
        $this->categoriaId = $id;
        $this->search = '';
    }

    public function addToCart($productoId)
    {
        $producto = Producto::find($productoId);

        // 🔹 Agregar al carrito real en sesión/db aquí

        $this->dispatch('livewire:alert', [
            'message' => $producto->nombre . ' agregado al carrito',
            'type' => 'success'
        ]);

        $this->dispatch('productoAgregado');
    }

    #[On('setPerPageCategorias')]
    public function setPerPageCategorias($valor)
    {
        if ($this->perPage != $valor) {
            $this->perPage = $valor;
            $this->resetPage();
        }
    }

    public function render()
    {
        $locales = Local::orderBy('nombre')->get();
        $searchTerm = trim($this->search);

        // Traemos productos filtrados por local y búsqueda
        $productosQuery = Producto::query()
            ->when($this->localId, fn($q) => $q->where('local_id', $this->localId))
            ->when($this->categoriaId, fn($q) => $q->where('categoria_id', $this->categoriaId))
            ->when($searchTerm, fn($q) => $q->where(function($q2) use ($searchTerm) {
                $q2->where('nombre', 'like', "%{$searchTerm}%")
                ->orWhere('descripcion', 'like', "%{$searchTerm}%");
            }))
            ->with(['moneda', 'categoria']);

        $productosFiltrados = $productosQuery->get();

        // Traemos categorías como modelos Eloquent
        $categoriasIds = $productosFiltrados->pluck('categoria_id')->filter()->unique();
        $categorias = Categoria::whereIn('id', $categoriasIds)
            ->orderBy('orden_visual')
            ->orderBy('nombre')
            ->get();

        // Asociamos productos filtrados a cada categoría
        $categorias->each(function ($categoria) use ($productosFiltrados) {
            $categoria->productos_filtrados = $productosFiltrados
                ->where('categoria_id', $categoria->id)
                ->map(fn($p) => (object)[
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'descripcion' => $p->descripcion,
                    'imagen_url' => $p->imagen_url ?? null,
                    'precio' => $p->precio,
                    'moneda_codigo' => $p->moneda?->codigo,
                    'moneda_simbolo' => $p->moneda?->simbolo,
                    'moneda_color_text' => $p->moneda?->color_text,
                    'moneda_color_bg' => $p->moneda?->color_bg,
                    'stock' => $p->stock,
                    'oferta' => $p->oferta,
                ])->values(); // 🔹 reset indices
        });

        // Categorías selector (paginado)
        $categorias_selector = Categoria::query()
            ->when($this->localId, fn($q) => $q->where('local_id', $this->localId))
            ->orderBy('orden_visual')
            ->orderBy('nombre')
            ->paginate($this->perPage);

        return view('livewire.productos-por-categoria-final', [
            'locales' => $locales,
            'categorias' => $categorias,
            'categorias_selector' => $categorias_selector,
        ]);
    }
}
