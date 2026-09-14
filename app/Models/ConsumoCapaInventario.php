<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumoCapaInventario extends Model
{
    use HasFactory;

    protected $table = 'consumos_capas_inventario';

    protected $fillable = [
        'movimiento_inventario_detalle_id',
        'capa_inventario_id',
        'cantidad_base',
        'costo_unitario',
        'costo_total',
    ];

    protected $casts = [
        'cantidad_base' => 'decimal:10',
        'costo_unitario' => 'decimal:10',
        'costo_total' => 'decimal:10',
    ];

    public function movimientoDetalle(): BelongsTo
    {
        return $this->belongsTo(
            MovimientoInventarioDetalle::class,
            'movimiento_inventario_detalle_id'
        );
    }

    public function capaInventario(): BelongsTo
    {
        return $this->belongsTo(
            CapaInventario::class,
            'capa_inventario_id'
        );
    }
}