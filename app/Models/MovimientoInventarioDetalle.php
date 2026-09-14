<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MovimientoInventarioDetalle extends Model
{
    use HasTrazas;

    protected $table = 'movimientos_inventarios_detalles';

    protected $fillable = [
        'movimiento_inventario_id',
        'producto_id',
        'atributo_valor_id',

        'cantidad',
        'unidad_medida_original_id',
        'factor_aplicado',
        'cantidad_base',
        'unidad_medida_base_id',

        'costo_unitario_base',

        'tipo_ajuste',
    ];

    protected $casts = [
        'cantidad' => 'decimal:10',
        'factor_aplicado' => 'decimal:10',
        'cantidad_base' => 'decimal:10',
        'costo_unitario_base' => 'decimal:10',
    ];

    public function movimiento(): BelongsTo
    {
        return $this->belongsTo(
            MovimientoInventario::class
        );
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(
            Producto::class
        );
    }

    public function atributoValor(): BelongsTo
    {
        return $this->belongsTo(
            AtributoValor::class
        );
    }

    public function unidadMedidaOriginal(): BelongsTo
    {
        return $this->belongsTo(
            UnidadMedida::class,
            'unidad_medida_original_id'
        );
    }

    public function unidadMedidaBase(): BelongsTo
    {
        return $this->belongsTo(
            UnidadMedida::class,
            'unidad_medida_base_id'
        );
    }

    public function consumosCapas(): HasMany
    {
        return $this->hasMany(
            ConsumoCapaInventario::class,
            'movimiento_inventario_detalle_id'
        );
    }
}