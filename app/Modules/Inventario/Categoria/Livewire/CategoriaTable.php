<?php

namespace App\Modules\Inventario\Categoria\Livewire;

use App\Core\Tables\TableComponent;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use App\Http\Livewire\Traits\HasReorderableItems;
use App\Models\Categoria;

class CategoriaTable extends TableComponent
{
    use HasReorderableItems;

    #[Reactive]
    public ?int $localId = null;

    public ?int $page = 1;


    public function mount(?int $localId = null): void
    {
        $this->localId = $localId;
    }

    #[On('categoria-guardada')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    #[On('categoria-eliminada')]
    public function categoriaEliminada(): void
    {
        $total = Categoria::query()
                    ->where('local_id', $this->localId)
                    ->count();

        $lastPage = max(1, (int) ceil($total / $this->perPage));

        \Log::info('CategoriaTable::categoriaEliminada', [
            'localId' => $this->localId,
            'page' => $this->page,
            'perPage' => $this->perPage,
            'total' => $total,
            'lastPage' => $lastPage,
        ]);

        if ($this->page > $lastPage) {
            $this->setPage($lastPage);

            \Log::info('CategoriaTable::categoriaEliminada - pagina corregida', [
                'page' => $this->page,
            ]);
        }

    }

    // protected function tableOptions(): array
    // {
    //     return [];
    // }

    protected function sortable(): bool
    {
        return true;
    }

    protected function sortableMethod(): ?string
    {
        return 'reorderCategorias';
    }

    protected function sortableOptions(): array
    {
        return [
            'animation' => 150,
        ];
    }

    public function reorderCategorias(array $orderedIds): void
    {
        if (!$this->localId) {
            return;
        }

        $this->reorderItems(
            $orderedIds,
            Categoria::class,
            fn ($query) => $query->where(
                'local_id',
                $this->localId
            )
        );

        $this->resetPage();

        $this->dispatch('categoria-orden-actualizado');
    }

    protected function tableClass(): string
    {
        return \App\Modules\Inventario\Categoria\Tables\CategoriaTableDefinition::class;
    }

    protected function tableParameters(): array
    {
        // logger()->info('TABLE PARAMS', [
        //     'local' => $this->localId,
        //     'categoria' => $this->categoriaId,
        // ]);
        return [
            'localId' => $this->localId,
        ];
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando categorías...',
        ]);
    }
}