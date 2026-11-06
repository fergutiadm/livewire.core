<?php

namespace App\Modules\Ayuda\AyudaVisor\Livewire;

use App\Models\AyudaArticulo;
use App\Models\AyudaCategoria;
use Livewire\Component;

class AyudaVisorPage extends Component
{
    public string $buscar = '';

    public ?string $contexto = null;

    public ?int $articuloId = null;

    public function mount(?string $contexto = null): void
    {
        $this->contexto = $contexto;
    }

    public function seleccionarArticulo(int $id): void
    {
        $articulo = AyudaArticulo::publicados()
            ->when(
                $this->contexto,
                fn ($query) => $query->porContexto($this->contexto)
            )
            ->whereKey($id)
            ->first();

        if ($articulo) {
            $this->articuloId = $articulo->id;
        }
    }

    public function updatedBuscar(): void
    {
        $this->articuloId = null;
    }

    public function render()
    {
        $categorias = AyudaCategoria::activas()
            ->ordenadas()
            ->withCount([
                'articulos as articulos_publicados_count' =>
                    fn ($query) => $query->publicados(),
            ])
            ->get();

        $articulos = AyudaArticulo::query()
            ->publicados()
            ->with('categoria')
            ->when(
                $this->contexto,
                fn ($query) => $query->porContexto($this->contexto)
            )
            ->when(
                trim($this->buscar) !== '',
                function ($query) {
                    $buscar = trim($this->buscar);

                    $query->where(function ($q) use ($buscar) {
                        $q->where('titulo', 'like', "%{$buscar}%")
                          ->orWhere('resumen', 'like', "%{$buscar}%");
                    });
                }
            )
            ->ordenados()
            ->get();

        $articulo = $this->articuloId
            ? AyudaArticulo::publicados()
                ->whereKey($this->articuloId)
                ->first()
            : null;

        return view('modules.ayuda.ayuda-visor.page', [
            'categorias' => $categorias,
            'articulos' => $articulos,
            'articulo' => $articulo,
        ]);
    }
}
