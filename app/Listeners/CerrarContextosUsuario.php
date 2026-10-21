<?php

namespace App\Listeners;

use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class CerrarContextosUsuario
{
    protected LocalContext $localContext;
    protected PeriodoContext $periodoContext;

    protected ?int $localId = null;
    protected ?int $periodoId = null;

    /**
     * Inyectamos los contextos del sistema mediante el contenedor de Laravel.
     */
    public function __construct(LocalContext $localContext, PeriodoContext $periodoContext)
    {
        $this->localContext = $localContext;
        $this->periodoContext = $periodoContext;
    }

    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        $this->localId = app(LocalContext::class)->id();
        $this->periodoId = app(PeriodoContext::class)->id();
    }
}
