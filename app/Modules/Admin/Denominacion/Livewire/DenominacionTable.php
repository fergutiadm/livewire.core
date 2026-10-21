<?php

namespace App\Modules\Admin\Denominacion\Livewire;

use App\Core\Tables\TableComponent;
use App\Models\Denominacion;
use App\Modules\Admin\Denominacion\Actions\ToggleDenominacionActivaAction;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Reactive;

//use App\Modules\Admin\Denominacion\Tables\DenominacionTableDefinition;

class DenominacionTable extends TableComponent
{
    #[Reactive]
    public ?int $monedaId = null;

    protected function tableClass(): string
    {
        return \App\Modules\admin\Denominacion\Tables\DenominacionTableDefinition::class;
    }

    public function toggleActiva(int $denominacionId): void
    {
        $denominacion = Denominacion::findOrFail($denominacionId);

        app(ToggleDenominacionActivaAction::class)->execute($denominacion);
    }

    public function mount(?int $monedaId = null): void
    {
        $this->monedaId = $monedaId;

        /*Log::info('DenominacionTable->mount', [
             'monedaId' => $monedaId
         ]);*/
    }

    protected function tableParameters(): array
    {
        // logger()->info('TABLE PARAMS', [
        //     'moneda' => $this->monedaId,
        // ]);
        return [
            'monedaId' => $this->monedaId,
        ];
    }


    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando denominaciones...',
        ]);
    }
}