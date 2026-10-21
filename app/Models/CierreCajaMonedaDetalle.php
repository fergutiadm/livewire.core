<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CierreCajaMonedaDetalle extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'cierres_caja_moneda_detalles';

    protected $fillable = [
        'cierre_caja_moneda_id',
        'denominacion_id',
        'valor_denominacion',
        'cantidad_billetes',
        'subtotal',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function cierre_caja_moneda(): BelongsTo
    {
        return $this->belongsTo(CierreCajaMoneda::class, 'cierre_caja_moneda_id');
    }

    public function denominacion(): BelongsTo
    {
        return $this->belongsTo(Denominacion::class, 'denominacion_id');
    }

    /* =====================
     |  GETTERS
     ===================== */
     public function getSubtotal()
     {
        return $this->valor_denominacion * $this->cantidad_billetes;
     }
}
