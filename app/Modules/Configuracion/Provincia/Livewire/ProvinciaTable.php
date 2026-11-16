<?php

namespace App\Modules\Configuracion\Provincia\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\Provincia;
use App\Modules\Configuracion\Provincia\Actions\ToggleProvinciaActivaAction;
use App\Modules\Configuracion\Provincia\Actions\ToggleProvinciaPorDefectoAction;

//use App\Modules\Configuracion\Provincia\Tables\ProvinciaTableDefinition;

class ProvinciaTable extends TableComponent
{
    protected bool $stopLoadingAfterRender = false;

    public function toggleActiva(int $provinciaId): void
    {
        $provincia = Provincia::findOrFail($provinciaId);

        app(ToggleProvinciaActivaAction::class)->execute($provincia);
    }

    public function togglePorDefecto(int $provinciaId): void
    {
        $provincia = Provincia::findOrFail($provinciaId);

        app(ToggleProvinciaPorDefectoAction::class)->execute($provincia);
    }

    public function rendered(): void
    {
        if ($this->stopLoadingAfterRender) {
            $this->stopLoadingAfterRender = false;
            $this->dispatch('loading-stop');
        }
    }

    protected function tableClass(): string
    {
        return \App\Modules\configuracion\Provincia\Tables\ProvinciaTableDefinition::class;
    }
}