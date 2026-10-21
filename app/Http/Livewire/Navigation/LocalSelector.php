<?php

namespace App\Http\Livewire\Navigation;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Models\Local;
use App\Models\PeriodoContable;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class LocalSelector extends Component
{
    public bool $showModal = false;

    public ?int $localId = null;

    public $locales;

    public function mount(LocalContext $localContext): void
    {
        $this->localId = $localContext->id();

        $this->locales = Local::orderByDesc('es_principal')->orderBy('nombre')->get();

        if (!$this->localId && $this->locales->isNotEmpty()) {
            $this->localId = $this->locales->firstWhere('es_principal', true)->id;

            $localContext->set($this->localId);
            Log::info('LocalSelector - es_principal', 
                [
                    '$localId' => $this->localId,
                ]
            );  
        }
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function seleccionarLocal(int $localId, LocalContext $localContext): void
    {
        $local = $this->locales->firstWhere('id', $localId);

        if (!$local) {
            return;
        }

        $localContext->set($localId);

        $this->localId = $localId;
        $this->showModal = false;

        $this->dispatch(
            'local-context-changed',
            localId: $localId
        );

        $periodoContableVigenteId = PeriodoContable::query()->where('activo', true)
                 ->where('cerrado', false)->where('local_id', $localId)->value('id');

        Log::info('LocalSelector - seleccionarLocal', 
            [
                '$localId' => $localId,
                '$periodoContableVigenteId'=>$periodoContableVigenteId
            ]
        );
        
        $this->dispatch(
            'periodo-context-changed',
            periodoId: $periodoContableVigenteId
        );
    }

    public function render()
    {
        $localActual = $this->locales->firstWhere('id', $this->localId);

        return view('navigation.local-selector', [
            'localActual' => $localActual,
        ]);
    }
}