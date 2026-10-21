<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CierreCaja extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'cierres_caja';

    protected $fillable = [
        'local_id',
        'caja_id',
        'periodo_contable_id',
        'operador_id',
        'fecha',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function periodo_contable(): BelongsTo
    {
        return $this->belongsTo(PeriodoContable::class);
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cierres_caja_moneda(): HasMany
    {
        return $this->HasMany(CierreCajaMoneda::class, 'cierre_caja_id');
    }

    /* =====================================================
     |  SCOPES (mínimos, reutilizables)
     ===================================================== */

    public function scopeDeLocal(Builder $q, int $localId): Builder
    {
        return $q->where('local_id', $localId);
    }

    public function scopeDeCaja(Builder $q, int $cajaId): Builder
    {
        return $q->where('caja_id', $cajaId);
    }

    public function scopeDePeriodo(Builder $q, int $periodoId): Builder
    {
        return $q->where('periodo_contable_id', $periodoId);
    }

    public function scopeDeOperador(Builder $q, int $operadorId): Builder
    {
        return $q->where('operador_id', $operadorId);
    }

    public function scopeDeLocalCaja(Builder $q, int $localId, int $cajaId): Builder
    {
        return $q->DeLocal($localId)->DeCaja($cajaId);
    }

    public function scopeDeLocalCajaPeriodo(Builder $q, int $localId, int $cajaId, int $periodoId): Builder
    {
        return $q->DeLocal($localId)->DeCaja($cajaId)->DePeriodo($periodoId);
    }

    public function scopeDeLocalCajaPeriodoOperdador(Builder $q, int $localId, int $cajaId, int $periodoId, int $operdadorId): Builder
    {
        return $q->DeLocal($localId)->DeCaja($cajaId)->DePeriodo($periodoId)->DeOperador($operdadorId);
    }

    public function scopeDeLocalPeriodo(Builder $q, int $localId, int $periodoId): Builder
    {
        return $q->DeLocal($localId)->DePeriodo($periodoId);
    }

    public function scopeDeLocalPeriodoOperdador(Builder $q, int $localId, int $periodoId, int $operdadorId): Builder
    {
        return $q->DeLocal($localId)->DePeriodo($periodoId)->DeOperador($operdadorId);
    }

    public function scopeDeLocalOperdador(Builder $q, int $localId, int $operdadorId): Builder
    {
        return $q->DeLocal($localId)->DeOperador($operdadorId);
    }
}
