<?php

namespace App\Services;

use App\Models\PeriodoContable;

class FechaPeriodoService
{
    public function fecha(int $periodoId, ?string $fechaOperacion = null): ?string
    {
        // 1. Buscamos el periodo contable actual en la base de datos
        $periodoContable = PeriodoContable::find($periodoId);

        $hoy = now()->format('Y-m-d');

        if ($periodoContable) {
            // 2. Asignamos las fechas límite (soportando los nombres de columna que tengas)
            $fechaMinima = $periodoContable->fecha_inicio ?? $periodoContable->fecha_inicial ?? null;
            $fechaMaxima = $periodoContable->fecha_fin ?? $periodoContable->fecha_final ?? null;
        }else{
            return $hoy;
        }

        $fechaPeriodo = \Carbon\Carbon::parse($fechaOperacion)->format('Y-m-d') ?? $hoy;
        if($fechaPeriodo < $fechaMinima || $fechaPeriodo > $fechaMaxima){
            $fechaPeriodo = ($hoy < $fechaMinima) ? $fechaMinima : (($hoy > $fechaMaxima) ? $fechaMaxima : $hoy);
        }

        return $fechaPeriodo->format('Y-m-d');
    }
}