<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class VentaProducto extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $fillable = [
        'venta_id',
        'producto_id',
        'moneda_id',
        'cantidad',
        'costo',
        'precio',
        'tasa_cambio',
        'descuento',
        'mano_obra',
        'tipo',
    ];

    protected $casts = [
        'tipo' => 'array',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function moneda()
    {
        return $this->belongsTo(Moneda::class);
    }

    public function atributable(): MorphMany
    {
        return $this->morphMany(AtributablesValoresMovimientos::class, 'atributable');
    }

    // Opcional: constantes para evitar “magic strings”
    public const TIPO_PRODUCTO   = 'producto';
    public const TIPO_SERVICIO   = 'servicio';

    /* ===================== Helpers / Scopes ===================== */

    public function scopeProducto($q)
    {
        return $q->where('tipo', self::TIPO_PRODUCTO);
    }

    public function scopeServicio($q)
    {
        return $q->where('type', self::TIPO_SERVICIO);
    }
}
