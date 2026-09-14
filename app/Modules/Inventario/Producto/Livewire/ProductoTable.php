<?php

namespace App\Modules\Inventario\Producto\Livewire;

use App\Core\Tables\TableComponent;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

use Livewire\Attributes\Reactive;

class ProductoTable extends TableComponent
{
    #[Reactive]
    public ?int $localId = null;
    #[Reactive]
    public ?int $categoriaId = null;

    public ?int $page = 1;


    public function mount(?int $localId = null, ?int $categoriaId = null): void
    {
        $this->localId = $localId;
        $this->categoriaId = $categoriaId;

        // Log::info('ProductoTable->mount', [
        //     'localId' => $localId,
        //     'categoriaId' => $categoriaId
        // ]);
    }


    #[On('producto-guardado')]
    public function refreshTable(): void
    {

        // $this->resetPage();
    }

    protected function tableClass(): string
    {
        return \App\Modules\Inventario\Producto\Tables\ProductoTableDefinition::class;
    }

    protected function tableParameters(): array
    {
        return [
            'localId' => $this->localId,
            'categoriaId' => $this->categoriaId,
        ];
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando productos...',
        ]);
    }

    // public function dehydrate(): void
    // {
    //     $inicio = microtime(true);

    //     logger()->info('TABLE DEHYDRATE - inicio', [
    //         'time' => $inicio,
    //         'search' => $this->search,
    //         'page' => $this->page,
    //         'perPage' => $this->perPage,
    //         'filters' => $this->filters,
    //     ]);

    //     logger()->info('TABLE DEHYDRATE - fin', [
    //         'time' => microtime(true),
    //         'elapsed_ms' => round(
    //             (microtime(true) - $inicio) * 1000,
    //             2
    //         ),
    //     ]);
    // }
}
