<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonedaFondo extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'moneda_fondos';

    protected $fillable = [
        'local_id',
        'periodo_id',
        'moneda_id',
        'generado_por_id',
        'concepto_id',
        'descripcion',
        'monto',
        'tasa_cambio',
        'desglose',
        'fecha',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'tasa_cambio' => 'decimal:10',
        'fecha' => 'date:Y-m-d',
        'desglose' => 'array',
    ];

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class, 'local_id');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(PeriodoContable::class, 'periodo_id');
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class, 'moneda_id');
    }

    public function generadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generado_por_id');
    }

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(
            ConceptoMonedaFondo::class,
            'concepto_id'
        );
    }
}