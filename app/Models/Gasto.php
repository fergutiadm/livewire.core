<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gasto extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $table = 'gastos';    

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'local_id',
        'tipo_gasto_id',
        'periodo_contable_id',
        'especialista_id',
        'responsable_id',
        'operador_id',
        'cantidad',
        'valor',
        'moneda_id',
        'tasa_cambio',
        'tasa_cambio_usd',
        'chequeado',
        'fecha_chequeado',
        'ip_var',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function tipoGasto(): BelongsTo
    {
        return $this->belongsTo(TipoGasto::class);
    }

    public function periodoContable(): BelongsTo
    {
        return $this->belongsTo(PeriodoContable::class);
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function gastosPagos(): HasMany
    {
        return $this->hasMany(GastoPago::class);
    }
}
