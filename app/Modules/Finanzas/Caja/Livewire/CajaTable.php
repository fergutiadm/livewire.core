<?php

namespace App\Modules\Finanzas\Caja\Livewire;

use App\Core\Context\LocalContext;
use App\Core\Tables\TableComponent;

use App\Models\Caja;
use App\Modules\Finanzas\Caja\Actions\ToggleCajaActivaAction;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class CajaTable extends TableComponent
{
    public ?int $localId = null;

    protected bool $stopLoadingAfterRender = false;

    public function mount(): void
    {
        $this->localId = app(LocalContext::class)->id();
    }

    #[On('local-context-changed')]
    public function localContextChanged(int $localId): void
    {
        $this->localId = $localId;
        $this->stopLoadingAfterRender = true;
    }

    protected function tableClass(): string
    {
        return \App\Modules\Finanzas\Caja\Tables\CajaTableDefinition::class;
    }

    public function toggleActiva(int $cajaId): void
    {
        $caja = Caja::findOrFail($cajaId);

        app(ToggleCajaActivaAction::class)->execute($caja);
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
        Log::info('tableParameters',
            [
                'localId' => $this->localId,
            ]
        );
        return [
            'localId' => $this->localId,
        ];
    }

    public function placeholder()
    {
        return view('components.loading-placeholder', [
            'message' => 'Cargando cajas...',
        ]);
    }
}
