<?php

namespace App\Modules\Inventario\UnidadMedida\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\UnidadMedida;
use App\Modules\Inventario\UnidadMedida\Actions\ToggleUnidadMedidaActivaAction;
use Livewire\Attributes\On;

//use App\Modules\Inventario\UnidadMedida\Tables\UnidadMedidaTableDefinition;

class UnidadMedidaTable extends TableComponent
{
    public ?int $unidadDimensionId = null;
    protected bool $stopLoadingAfterRender = false;

    protected function tableClass(): string
    {
        return \App\Modules\inventario\UnidadMedida\Tables\UnidadMedidaTableDefinition::class;
    }

    public function toggleActiva(int $unidadMedidaId): void
    {
        $unidadMedida = UnidadMedida::findOrFail($unidadMedidaId);

        app(ToggleUnidadMedidaActivaAction::class)->execute($unidadMedida);
    }

    #[On('unidad-medida-tabla-refrescar')]
    public function mapearFiltrosYRefrescar(array $filtros): void
    {
        $this->unidadDimensionId = $filtros['unidadDimensionId'] ?? $this->unidadDimensionId;

        $this->stopLoadingAfterRender = true;
    }

    #[On('unidad-medida-limpiar-filtros')]
    public function limpiarFiltros(): void
    {
        $this->reset(
            'unidadDimensionId',
        );
    }

    protected function tableParameters(): array
    {
        return [
            'unidadDimensionId' => $this->unidadDimensionId,
        ];
    }

    public function rendered(): void
    {
        if ($this->stopLoadingAfterRender) {
            $this->stopLoadingAfterRender = false;
            $this->dispatch('loading-stop');
        }
    }
}