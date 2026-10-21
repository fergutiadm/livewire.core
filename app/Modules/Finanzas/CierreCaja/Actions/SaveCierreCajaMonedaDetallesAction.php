<?php

namespace App\Modules\Finanzas\CierreCaja\Actions;

use App\Models\CierreCajaMoneda;
use App\Models\CierreCajaMonedaDetalle;
use App\Models\Denominacion;
use App\Modules\Finanzas\CierreCaja\DTOs\SaveCierreCajaMonedaDetallesDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveCierreCajaMonedaDetallesAction
{
    public function execute(
        SaveCierreCajaMonedaDetallesDTO $dto
    ): void {
        DB::transaction(function () use ($dto): void {
            $cierreCajaMoneda = CierreCajaMoneda::query()
                ->lockForUpdate()
                ->findOrFail($dto->cierreCajaMonedaId);

            $totalContado = '0.00';
            $detalles = [];

            foreach ($dto->cards as $denominacionId => $card) {
                $denominacionId = (int) $denominacionId;

                $valor = (string) (
                    $card['valor_denominacion'] ?? '0'
                );

                $cantidad = filter_var(
                    $card['cantidad_billetes'] ?? null,
                    FILTER_VALIDATE_INT
                );

                if (
                    $denominacionId <= 0
                    || bccomp($valor, '0', 2) <= 0
                    || $cantidad === false
                    || $cantidad < 0
                ) {
                    throw ValidationException::withMessages([
                        'cards' => 'Hay datos inválidos en el conteo de denominaciones.',
                    ]);
                }

                // Verificar que la denominación pertenece
                // a la moneda del cierre.
                $denominacion = Denominacion::query()
                    ->whereKey($denominacionId)
                    ->where(
                        'moneda_id',
                        $cierreCajaMoneda->moneda_id
                    )
                    ->first();

                if (!$denominacion) {
                    throw ValidationException::withMessages([
                        'cards' => 'Una denominación no pertenece a la moneda del cierre.',
                    ]);
                }

                // Utilizar el valor del catálogo como fuente
                // de verdad para una denominación activa.
                // Las inactivas conservan su valor histórico.
                $valorCatalogo = (string) $denominacion->valor;

                $detalleExistente = CierreCajaMonedaDetalle::query()
                    ->where(
                        'cierre_caja_moneda_id',
                        $cierreCajaMoneda->id
                    )
                    ->where('denominacion_id', $denominacionId)
                    ->first();

                if ($denominacion->activa) {
                    $valor = $valorCatalogo;
                } elseif ($detalleExistente) {
                    $valor = (string) $detalleExistente->valor_denominacion;
                } else {
                    throw ValidationException::withMessages([
                        'cards' => 'No se puede crear un detalle nuevo con una denominación inactiva.',
                    ]);
                }

                $subtotal = bcmul(
                    $valor,
                    (string) $cantidad,
                    2
                );

                $totalContado = bcadd(
                    $totalContado,
                    $subtotal,
                    2
                );

                // Persistir solamente denominaciones contadas.
                if ($cantidad > 0) {
                    $detalles[] = [
                        'cierre_caja_moneda_id' => $cierreCajaMoneda->id,
                        'denominacion_id' => $denominacionId,
                        'valor_denominacion' => $valor,
                        'cantidad_billetes' => $cantidad,
                        'subtotal' => $subtotal,
                    ];
                }
            }

            // Reemplazar los detalles de forma atómica.
            CierreCajaMonedaDetalle::query()
                ->where(
                    'cierre_caja_moneda_id',
                    $cierreCajaMoneda->id
                )
                ->delete();

            foreach ($detalles as $detalle) {
                CierreCajaMonedaDetalle::query()->create($detalle);
            }

            // Mantener actualizado el total contado de la moneda.
            $cierreCajaMoneda->monto_contado = $totalContado;
            $cierreCajaMoneda->save();

        });
    }
}
