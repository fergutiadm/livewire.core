<?php

namespace App\Modules\Ayuda\AyudaContextual\Livewire;

use App\Models\AyudaArticulo;
use App\Modules\Ayuda\Services\AyudaHtmlSanitizer;
use Livewire\Component;

class AyudaContextual extends Component
{
    public string $contexto;

    public bool $abierto = false;

    public ?int $articuloId = null;

    public function mount(string $contexto): void
    {
        $this->contexto = $contexto;
    }

    public function abrir(): void
    {
        $this->abierto = true;
    }

    public function cerrar(): void
    {
        $this->abierto = false;
        $this->articuloId = null;
    }

    public function volverListado(): void
    {
        $this->articuloId = null;
    }

    public function seleccionarArticulo(int $id): void
    {
        $articulo = $this->consultaBase()
            ->whereKey($id)
            ->first();

        if ($articulo) {
            $this->articuloId = $articulo->id;
        }
    }

    private function consultaBase()
    {
        return AyudaArticulo::query()
            ->publicados()
            ->porContexto($this->contexto)
            ->with('categoria');
    }

    public function render()
    {
        $articulos = collect();
        $articulo = null;

        if ($this->abierto) {
            if ($this->articuloId !== null) {
                $articulo = $this->consultaBase()
                    ->whereKey($this->articuloId)
                    ->first();
            }

            if (!$articulo) {
                $this->articuloId = null;

                $articulos = $this->consultaBase()
                    ->ordenados()
                    ->get();
            }
        }

        return view('modules.ayuda.ayuda-contextual.panel', [
            'articulos' => $articulos,
            'articulo' => $articulo,
        ]);
    }
}