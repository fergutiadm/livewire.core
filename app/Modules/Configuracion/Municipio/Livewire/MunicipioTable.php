<?php

namespace App\Modules\Configuracion\Municipio\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\Municipio;
use App\Models\Provincia;
use App\Modules\Configuracion\Municipio\Actions\ToggleMunicipioActivoAction;
use App\Modules\Configuracion\Municipio\Actions\ToggleMunicipioPorDefectoAction;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use Livewire\Attributes\On;

//use App\Modules\Configuracion\Municipio\Tables\MunicipioTableDefinition;

class MunicipioTable extends TableComponent
{
    public ?int $provinciaId = null;

    protected bool $stopLoadingAfterRender = false;

    public function mount(): void
    {
        $provincia_default = app(SetupConfigService::class)->get('provincia_default');
        $this->provinciaId = Provincia::query()->where('slug', $provincia_default)->value('id')??null;
    }

    #[On('municipio-tabla-refrescar')]
    public function mapearFiltrosYRefrescar(array $filtros): void
    {
        $this->provinciaId = $filtros['provinciaId'] ?? null;

        $this->stopLoadingAfterRender = true;
    }

    public function toggleActivo(int $municipioId): void
    {
        $municipio = Municipio::findOrFail($municipioId);

        app(ToggleMunicipioActivoAction::class)->execute($municipio);
    }

    public function togglePorDefecto(int $municipioId): void
    {
        $municipio = Municipio::findOrFail($municipioId);

        app(ToggleMunicipioPorDefectoAction::class)->execute($municipio);
    }

    public function rendered(): void
    {
        if ($this->stopLoadingAfterRender) {
            $this->stopLoadingAfterRender = false;
            $this->dispatch('loading-stop');
        }
    }

    protected function tableParameters(): array
    {
        return [
            'provinciaId' => $this->provinciaId,
        ];
    }

    #[On('municipio-limpiar-filtros')]
    public function limpiarFiltros(): void
    {
        $this->reset(
            'provinciaId',
        );
    }

    protected function tableClass(): string
    {
        return \App\Modules\configuracion\Municipio\Tables\MunicipioTableDefinition::class;
    }
}