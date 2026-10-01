<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GastoPago extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'gastos_pagos';

    protected bool $includeAllOnDelete = true;

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'gasto_id',
        'metodo',
        'tarjeta_magnetica_id',
        'numero_tarjeta',
        'moneda_id',
        'cantidad',
        'tasa_cambio',
        'fecha_pago',
        'operador_id',
        'descripcion',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
