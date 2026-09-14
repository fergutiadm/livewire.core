<?php

namespace App\Modules\Contabilidad\PeriodoContable\Livewire;

use App\Core\Tables\TableComponent;
//use App\Modules\Contabilidad\PeriodoContable\Tables\PeriodoContableTableDefinition;

use Livewire\Attributes\Reactive;

class PeriodoContableTable extends TableComponent
{
    #[Reactive]
    public ?int $localId = null;

    // public ?int $page = 1;


    public function mount(?int $localId = null): void
    {
        $this->localId = $localId;
    }

    protected function tableClass(): string
    {
        return \App\Modules\Contabilidad\PeriodoContable\Tables\PeriodoContableTableDefinition::class;
    }

    protected function tableParameters(): array
    {
        return [
            'localId' => $this->localId,
        ];
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando periodos contables...',
        ]);
    }

}
