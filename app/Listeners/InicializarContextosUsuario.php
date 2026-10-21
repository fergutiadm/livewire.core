<?php

namespace App\Listeners;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Http\Livewire\Navigation\FechaOperacionSelector;
use App\Models\Local;
use App\Models\PeriodoContable;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class InicializarContextosUsuario
{
    protected LocalContext $localContext;
    protected PeriodoContext $periodoContext;

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
    public function handle(Login $event): void
    {
        // 1. Intentar obtener el primer local ordenado por nombre
        $primerLocal = Local::where('es_principal', true)->first();

        if (!$primerLocal) {
            return; // Detener si no existen locales registrados
        }

        // 2. Establecer el contexto del Local por defecto
        $this->localContext->set($primerLocal->id);

        // 3. Buscar el período contable vigente de ese local específico
        $periodoVigente = PeriodoContable::query()
            ->where('local_id', $primerLocal->id)
            ->where('activo', true)
            ->where('cerrado', false)
            ->first();

        // 4. Si existe un período vigente, lo asignamos. Si no, asignamos el primero disponible del local.
        if ($periodoVigente) {
            $this->periodoContext->set($periodoVigente->id);
                app(FechaOperacionSelector::class)->actualizarFechaOperacion($periodoVigente->id);
        } else {
            $primerPeriodo = PeriodoContable::where('local_id', $primerLocal->id)
                ->orderByDesc('activo')
                ->orderByDesc('fecha_inicio')
                ->first();

            if ($primerPeriodo) {
                $this->periodoContext->set($primerPeriodo->id);
                app(FechaOperacionSelector::class)->actualizarFechaOperacion($primerPeriodo->id);
            }
        }
    }
}
