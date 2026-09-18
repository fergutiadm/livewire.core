<?php

namespace App\Modules\Inventario\Atributo\Livewire;

use App\Core\Tables\TableComponent;
use App\Http\Livewire\Traits\HasReorderableItems;
use App\Models\Atributo;
use Livewire\Attributes\On;

class AtributoTable extends TableComponent
{
    use HasReorderableItems;

    #[On('atributo-guardado')]
    public function refreshTable(): void
    {
        //
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
            Atributo::class
        );

        $this->resetPage();

        $this->dispatch(
            'atributo-orden-actualizado'
        );
    }

    public function toggleActivo(int $id): void
    {
        $atributo = Atributo::query()
            ->findOrFail($id);

        $activoNuevo = !$atributo->activo;

        $this->command(
            'atributo.update',
            [
                'id' => $atributo->id,
                'codigo' => $atributo->codigo,
                'nombre' => $atributo->nombre,
                'descripcion' => $atributo->descripcion,
                'orden_visual' => $atributo->orden_visual,
                'activo' => $activoNuevo,
            ]
        );

        $this->dispatch(
            'livewire:alert',
            [
                'message' => $activoNuevo
                    ? 'Atributo activado correctamente...'
                    : 'Atributo desactivado correctamente...',
                'type' => 'success',
            ]
        );

        $this->dispatch('loading-stop');
    }

    protected function tableClass(): string
    {
        return \App\Modules\Inventario\Atributo\Tables\AtributoTableDefinition::class;
    }

    public function placeholder()
    {
        return view(
            'components.loading-placeholder',
            [
                'message' => 'Cargando atributos...',
            ]
        );
    }
}