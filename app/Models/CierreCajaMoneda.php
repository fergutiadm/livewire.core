<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CierreCajaMoneda extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'cierres_caja_monedas';

    protected $fillable = [
        'cierre_caja_id',
        'moneda_id',
        'monto_esperado',
        'monto_contado',
        'estado',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function cierre_caja(): BelongsTo
    {
        return $this->belongsTo(CierreCaja::class, 'cierre_caja_id');
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class, 'moneda_id');
    }

    public function cierres_caja_moneda_detalles(): HasMany
    {
        return $this->HasMany(CierreCajaMonedaDetalle::class, 'cierre_caja_moneda_id');
    }
}
