<?php

namespace App\Modules\Finanzas\CierreCaja\Actions;

use App\Models\CierreCaja;
use App\Modules\Finanzas\CierreCaja\DTOs\UpdateCierreCajaDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateCierreCajaAction
{
    public function __invoke(UpdateCierreCajaDTO $dto): CierreCaja
    {
        return DB::transaction(function () use ($dto) {
            $this->validarMonedas($dto->cierreMonedas);

            $cierreCaja = CierreCaja::query()
                ->lockForUpdate()
                ->findOrFail($dto->id);

            $monedasRecibidas = collect($dto->cierreMonedas);

            $idsMonedasRecibidas = $monedasRecibidas
                ->pluck('moneda_id')
                ->map(fn ($id) => (int) $id);

            if ($idsMonedasRecibidas->duplicates()->isNotEmpty()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cierreMonedas' => 'No se permite repetir una moneda en el cierre.',
                ]);
            }

            $cierreCaja->update($dto->attributes());

            $monedaIds = array_map(
                fn (array $moneda) => (int) $moneda['moneda_id'],
                $dto->cierreMonedas
            );

            // Eliminar monedas que fueron retiradas del formulario.
            // Sus detalles de denominaciones se eliminan por cascada.
            $cierreCaja->cierres_caja_moneda()
                ->whereNotIn('moneda_id', $monedaIds)
                ->delete();

            // Crear o actualizar las monedas que permanecen.
            foreach ($dto->cierreMonedas as $moneda) {
                $cierreCaja->cierres_caja_moneda()->updateOrCreate(
                    [
                        'moneda_id' => (int) $moneda['moneda_id'],
                    ],
                    [
                        'monto_esperado' => $moneda['monto_esperado'],
                        'monto_contado' => $moneda['monto_contado'],
                        'estado' => $moneda['estado'] ?? 'pendiente',
                    ]
                );
            }

            return $cierreCaja->load('cierres_caja_moneda');
        });
    }

    private function validarMonedas(array $monedas): void
    {
        if (count($monedas) === 0) {
            throw ValidationException::withMessages([
                'cierreMonedas' => 'Debe agregar al menos una moneda al cierre.',
            ]);
        }

        $monedaIds = [];

        foreach ($monedas as $moneda) {
            if (
                !isset($moneda['moneda_id']) ||
                !is_numeric($moneda['moneda_id']) ||
                (int) $moneda['moneda_id'] <= 0
            ) {
                throw ValidationException::withMessages([
                    'cierreMonedas' => 'Cada registro debe tener una moneda válida.',
                ]);
            }

            $monedaId = (int) $moneda['moneda_id'];

            if (in_array($monedaId, $monedaIds, true)) {
                throw ValidationException::withMessages([
                    'cierreMonedas' => 'No puede agregar la misma moneda más de una vez.',
                ]);
            }

            $monedaIds[] = $monedaId;

            foreach (['monto_esperado', 'monto_contado'] as $campo) {
                if (
                    !isset($moneda[$campo]) ||
                    !is_numeric($moneda[$campo]) ||
                    (float) $moneda[$campo] <= 0
                ) {
                    throw ValidationException::withMessages([
                        'cierreMonedas' => "El campo {$campo} debe ser mayor que cero.",
                    ]);
                }
            }
        }
    }
}
