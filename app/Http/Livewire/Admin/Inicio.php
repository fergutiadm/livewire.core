<?php

namespace App\Http\Livewire\Admin;

use App\Core\Context\LocalContext;
use App\Models\Atributo;
use App\Models\Categoria;
use App\Models\PeriodoContable;
use App\Models\Producto;
use Livewire\Component;

class Inicio extends Component
{
    public $periodoActivo;
    public $stats;
    public $ventasTotales;
    public $ultimosProductos;

    public function mount()
    {
        $localId = app(LocalContext::class)->id();
        $this->periodoActivo = PeriodoContable::with('local')
                                    ->where('local_id', $localId)
                                    ->where('activo', 1)
                                    ->where('cerrado', 0)
                                    ->latest('id')
                                    ->first();
        /*
        $this->periodoActivo = PeriodoContable::vigenteActual($localId)
                                    ->with('local')
                                    ->latest('id')
                                    ->first();
        */
        // Métricas base
        $this->stats = [
                'productos'   => Producto::count(),
                'categorias'  => Categoria::count(),
                'gestores'    => 0, //Gestores::count(),
                'atributos'   => Atributo::count(),
                'existencia'  => 0, //(int) Stock::sum('qty'),
        ];

        $this->ventasTotales = 0;

        // Últimos productos
        $this->ultimosProductos = Producto::with('categoria')
                                        ->latest('id')
                                        ->take(8)
                                        ->get();
    }

    public function render()
    {
        return view('livewire.admin.inicio')
            ->layout('layouts.app_admin');
    }
}
