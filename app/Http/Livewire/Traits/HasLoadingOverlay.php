<?php
namespace App\Http\Livewire\Traits;
use Livewire\Attributes\On;

trait HasLoadingOverlay {
    public bool $loading = false;
    public string $loadingMessage = '';

    #[On('loading-start')]
    public function loadingStart(string $message = 'Cargando...'): void
    {
        $this->loading = true;
        $this->loadingMessage = $message;
    }

    #[On('loading-stop')]
    public function loadingStop(): void
    {
        $this->loading = false;
        $this->loadingMessage = ''; }
    }
