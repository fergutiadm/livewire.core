<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CapaInventario extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'capas_inventario';

    protected $fillable = [
        'producto_id',
        'local_id',
        'movimiento_inventario_detalle_id',
        'capa_origen_id',
        'cantidad_inicial',
        'cantidad_disponible',
        'costo_unitario',
        'costo_total',
        'fecha',
    ];

    protected $casts = [
        'cantidad_inicial' => 'decimal:10',
        'cantidad_disponible' => 'decimal:10',
        'costo_unitario' => 'decimal:10',
        'costo_total' => 'decimal:10',
        'fecha' => 'datetime',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function movimientoDetalle(): BelongsTo
    {
        return $this->belongsTo(
            MovimientoInventarioDetalle::class,
            'movimiento_inventario_detalle_id'
        );
    }

    public function capaOrigen(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'capa_origen_id'
        );
    }
}