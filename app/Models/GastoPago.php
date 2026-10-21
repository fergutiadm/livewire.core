<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class GastoPago extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'gasto_pagos';

    protected bool $includeAllOnDelete = true;

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'gasto_id',
        'estado',
        'fecha_pago',
        'operador_id',
        'descripcion',
    ];

    public function gasto(): BelongsTo
    {
        return $this->belongsTo(Gasto::class);
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function desgloses(): HasMany
    {
        return $this->hasMany(GastoPagoDesglose::class);
    }

    protected function importeCup(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format(
                $this->calcularImporteCup(),
                2,
                '.',
                ''
            )
        );
    }

    public function calcularImporteCup(): float
    {
        return (float) $this->desgloses
            ->sum(function (GastoPagoDesglose $desglose) {
                return (float) $desglose->cantidad
                    * (float) $desglose->tasa_cambio;
            });
    }
}