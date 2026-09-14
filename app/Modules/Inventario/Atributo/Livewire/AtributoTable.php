<?php

namespace App\Modules\Inventario\Atributo\Livewire;

use App\Core\Tables\TableComponent;
use App\Http\Livewire\Traits\HasReorderableItems;
use App\Models\Atributo;
use Livewire\Attributes\On;

//use App\Modules\Inventario\Atributo\Tables\AtributoTableDefinition;

class AtributoTable extends TableComponent
{
    use HasReorderableItems;

    #[On('atributo-guardado')]
    public function refreshTable(): void
    {
        //$this->resetPage();
    }

    protected function sortable(): bool
    {
        return true;
    }

    protected function sortableMethod(): ?string
    {
        return 'reorderAtributos';
    }

    protected function sortableOptions(): array
    {
        return [
            'animation' => 150,
        ];
    }

    public function reorderAtributos(array $orderedIds): void
    {
        $this->reorderItems(
            $orderedIds,
            Atributo::class);

        $this->resetPage();

        $this->dispatch('atributo-orden-actualizado');
    }

    protected function tableClass(): string
    {
        return \App\Modules\inventario\Atributo\Tables\AtributoTableDefinition::class;
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando atributos...',
        ]);
    }
}