<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MeasureLivewireRequest
{
    public function handle(Request $request, Closure $next)
    {
        $inicio = microtime(true);

        Log::info('LIVEWIRE HTTP - INICIO', [
            'time' => $inicio,
            'uri' => $request->getRequestUri(),
        ]);

        $response = $next($request);

        Log::info('LIVEWIRE HTTP - DESPUES NEXT', [
            'time' => microtime(true),
            'elapsed_ms' => round(
                (microtime(true) - $inicio) * 1000,
                2
            ),
            'status' => $response->getStatusCode(),
        ]);

        return $response;
    }
}