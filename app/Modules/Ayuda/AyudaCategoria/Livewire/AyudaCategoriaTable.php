<?php

namespace App\Modules\Ayuda\AyudaCategoria\Livewire;

use App\Core\Tables\TableComponent;
use App\Http\Livewire\Traits\HasReorderableItems;
use App\Models\AyudaCategoria;
use App\Modules\Ayuda\AyudaCategoria\Actions\ToggleAyudaCategoriaActivaAction;
use Livewire\Attributes\On;

//use App\Modules\Ayuda\AyudaCategoria\Tables\AyudaCategoriaTableDefinition;

class AyudaCategoriaTable extends TableComponent
{
    use HasReorderableItems;

    public ?int $page = 1;


    public function mount(): void
    {
        //
    }

    #[On('ayuda-categoria-guardada')]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    #[On('ayuda-categoria-eliminada')]
    public function categoriaEliminada(): void
    {
        $total = AyudaCategoria::query()
                    ->count();

        $lastPage = max(1, (int) ceil($total / $this->perPage));

        if ($this->page > $lastPage) {
            $this->setPage($lastPage);
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
        $this->reorderItems(
            $orderedIds,
            AyudaCategoria::class
        );

        $this->resetPage();

        $this->dispatch('ayuda-categoria-orden-actualizado');
    }

    protected function tableClass(): string
    {
        return \App\Modules\Ayuda\AyudaCategoria\Tables\AyudaCategoriaTableDefinition::class;
    }

    public function toggleActiva(int $ayudaCategoriaId): void
    {
        $ayudaCategoria = AyudaCategoria::findOrFail($ayudaCategoriaId);

        app(ToggleAyudaCategoriaActivaAction::class)->execute($ayudaCategoria);
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando categorías...',
        ]);
    }
}