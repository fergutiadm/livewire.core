<?php

namespace App\Services\Inventario;

use App\Models\MovimientoInventario;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Exception;

class ConfirmarMovimientoInventarioService
{
    public function ejecutar(MovimientoInventario $movimiento): void
    {
        if ($movimiento->confirmado_at) {
            throw new Exception('Movimiento ya confirmado');
        }

        DB::transaction(function () use ($movimiento) {

            $movimiento->load('detalles');

            foreach ($movimiento->detalles as $detalle) {

                match ($movimiento->tipo) {
                    'entrada' => $this->procesarEntrada($movimiento, $detalle),
                    'salida' => $this->procesarSalida($movimiento, $detalle),
                    'traslado' => $this->procesarTraslado($movimiento, $detalle),
                    'ajuste' => $this->procesarAjuste($movimiento, $detalle),
                    default => throw new Exception('Tipo inválido')
                };
            }

            $movimiento->update([
                'confirmado_at' => now()
            ]);
        });
    }

    private function resolverStock($localId, $productoId, $atributoValorId = null): Stock
    {
        return Stock::firstOrCreate([
            'local_id' => $localId,
            'producto_id' => $productoId,
            'atributo_valor_id' => $atributoValorId,
        ], [
            'cantidad' => 0
        ]);
    }

    private function obtenerStockLock($localId, $productoId, $atributoValorId = null): Stock
    {
        $stock = Stock::where('local_id', $localId)
            ->where('producto_id', $productoId)
            ->where('atributo_valor_id', $atributoValorId)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            $stock = $this->resolverStock($localId, $productoId, $atributoValorId);
        }

        return $stock;
    }

    private function procesarEntrada($movimiento, $detalle): void
    {
        $stock = $this->obtenerStockLock(
            $movimiento->destino_local_id,
            $detalle->producto_id,
            $detalle->atributo_valor_id
        );

        $stock->increment('cantidad', $detalle->cantidad);
    }

    private function procesarSalida($movimiento, $detalle): void
    {
        $stock = $this->obtenerStockLock(
            $movimiento->origen_local_id,
            $detalle->producto_id,
            $detalle->atributo_valor_id
        );

        if ($stock->cantidad < $detalle->cantidad) {
            throw new Exception('Stock insuficiente');
        }

        $stock->decrement('cantidad', $detalle->cantidad);
    }

    private function procesarTraslado($movimiento, $detalle): void
    {
        $origen = $this->obtenerStockLock(
            $movimiento->origen_local_id,
            $detalle->producto_id,
            $detalle->atributo_valor_id
        );

        if ($origen->cantidad < $detalle->cantidad) {
            throw new Exception('Stock insuficiente traslado');
        }

        $destino = $this->obtenerStockLock(
            $movimiento->destino_local_id,
            $detalle->producto_id,
            $detalle->atributo_valor_id
        );

        $origen->decrement('cantidad', $detalle->cantidad);
        $destino->increment('cantidad', $detalle->cantidad);
    }

    private function procesarAjuste($movimiento, $detalle): void
    {
        $stock = $this->obtenerStockLock(
            $movimiento->origen_local_id,
            $detalle->producto_id,
            $detalle->atributo_valor_id
        );

        $stock->increment('cantidad', $detalle->cantidad);
    }
}
