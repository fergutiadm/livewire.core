<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $fillable = [
        'user_id',
        'cliente_id',
        'periodo_contable_id',
        'moneda_id',
        'tarjeta_magnetica_id',
        'descuento',
        'mano_obra',
        'fecha',
        'no_vale',
        'garantia_desde',
        'garantia_hasta',
        'pendiente',
        'al_por_mayor',
        'editada',
        'tipo',
        'descripcion',
    ];

    protected $casts = [
        'fecha'          => 'date',
        'garantia_desde' => 'date',
        'garantia_hasta' => 'date',
        'pendiente'      => 'boolean',
        'editada'        => 'array',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function periodo_contable(): BelongsTo
    {
        return $this->belongsTo(PeriodoContable::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function tarjeta_magnetica(): BelongsTo
    {
        return $this->belongsTo(TarjetaMagnetica::class);
    }

    public function movimientoInventario()
    {
        return $this->hasOne(
            MovimientoInventario::class
        );
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
