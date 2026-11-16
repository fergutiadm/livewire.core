<?php

namespace App\Modules\Finanzas\MonedaFondo\Actions;

use App\Models\ConceptoMonedaFondo;
use App\Models\Moneda;
use App\Models\MonedaFondo;
use App\Models\PeriodoContable;
use App\Modules\Finanzas\MonedaFondo\DTOs\CreateMonedaFondoDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateMonedaFondoAction
{
    public function __invoke(CreateMonedaFondoDTO $dto): MonedaFondo
    {
        $generadoPorId = Auth::id();

        if (!$generadoPorId) {
            throw ValidationException::withMessages([
                'generado_por_id' => 'Debe existir un usuario autenticado para registrar el movimiento.',
            ]);
        }

        return DB::transaction(function () use ($dto, $generadoPorId) {

            $periodo = PeriodoContable::query()
                ->whereKey($dto->periodo_id)
                ->lockForUpdate()
                ->first();

            if (!$periodo) {
                throw ValidationException::withMessages([
                    'periodo_id' => 'El periodo contable seleccionado no existe.',
                ]);
            }

            if ((int) $periodo->local_id !== $dto->local_id) {
                throw ValidationException::withMessages([
                    'periodo_id' => 'El periodo contable seleccionado no pertenece al local indicado.',
                ]);
            }

            if ((bool) $periodo->cerrado) {
                throw ValidationException::withMessages([
                    'periodo_id' => 'El periodo contable seleccionado está cerrado y no admite nuevos movimientos financieros.',
                ]);
            }

            $fecha = Carbon::parse($dto->fecha)->startOfDay();

            $inicio = Carbon::parse($periodo->fecha_inicio)->startOfDay();
            $fin = Carbon::parse($periodo->fecha_fin)->startOfDay();

            if ($fecha->lt($inicio) || $fecha->gt($fin)) {
                throw ValidationException::withMessages([
                    'fecha' => 'La fecha del movimiento debe estar dentro del periodo contable seleccionado.',
                ]);
            }

            $moneda = Moneda::query()
                ->whereKey($dto->moneda_id)
                ->lockForUpdate()
                ->first();

            if (!$moneda) {
                throw ValidationException::withMessages([
                    'moneda_id' => 'La moneda seleccionada no existe.',
                ]);
            }

            if (!(bool) $moneda->activa) {
                throw ValidationException::withMessages([
                    'moneda_id' => 'La moneda seleccionada está inactiva.',
                ]);
            }

            $concepto = ConceptoMonedaFondo::query()
                ->whereKey($dto->concepto_id)
                ->lockForUpdate()
                ->first();

            if (!$concepto) {
                throw ValidationException::withMessages([
                    'concepto_id' => 'El concepto seleccionado no existe.',
                ]);
            }

            if (!(bool) $concepto->activo) {
                throw ValidationException::withMessages([
                    'concepto_id' => 'El concepto seleccionado está inactivo.',
                ]);
            }

            $model = MonedaFondo::create([
                ...$dto->toArray(),
                'generado_por_id' => $generadoPorId,
            ]);

            return $model->refresh();
        });
    }
}
