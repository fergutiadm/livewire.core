<?php

namespace App\Modules\Ayuda\AyudaArticulo\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\AyudaArticulo;
use App\Modules\Ayuda\AyudaArticulo\Actions\ChangePublicationAyudaArticuloAction;
use App\Modules\Ayuda\AyudaArticulo\DTOs\ChangePublicationAyudaArticuloDTO;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class AyudaArticuloTable extends TableComponent
{
    #[Reactive]
    public ?int $categoriaId = null;

    public function mount(?int $categoriaId = null): void
    {
        $this->categoriaId = $categoriaId;
        $this->resetPage();
    }

    protected function sortable(): bool
    {
        return true;
    }

    protected function sortableMethod(): ?string
    {
        return 'reorderAyudaArticulo';
    }

    protected function sortableOptions(): array
    {
        return ['animation' => 150];
    }

    public function reorderAyudaArticulo(array $orderedIds): void
    {
        $this->reorderItems(
            $orderedIds,
            AyudaArticulo::class
        );

        $this->resetPage();

        $this->dispatch('ayuda-articulo-orden-actualizado');
    }

    protected function tableClass(): string
    {
        return \App\Modules\Ayuda\AyudaArticulo\Tables\AyudaArticuloTableDefinition::class;
    }

    protected function tableParameters(): array
    {
        return [
            'categoriaId' => $this->categoriaId,
        ];
    }

    public function togglePublicado(int $ayudaArticuloId): void
    {
        $articulo = AyudaArticulo::findOrFail($ayudaArticuloId);

        try {
            app(ChangePublicationAyudaArticuloAction::class)(
                new ChangePublicationAyudaArticuloDTO(
                    id: $articulo->id,
                    publicado: !$articulo->publicado,
                )
            );

            $this->dispatch('ayuda-articulo-guardado');

        } catch (ValidationException $e) {
            $this->dispatch('livewire:alert', [
                'message' => $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando artículos de ayuda...',
        ]);
    }
}