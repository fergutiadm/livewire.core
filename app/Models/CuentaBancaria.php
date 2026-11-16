<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuentaBancaria extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'cuentas_bancarias';

    protected $fillable = [
        'sucursal_id',
        'moneda_id',
        'numero_cuenta',
        'titular',
        'descripcion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /**
     * RELACIONES
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(
            BancoSucursal::class,
            'sucursal_id'
        );
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(
            Moneda::class,
            'moneda_id'
        );
    }

    /**
     * SCOPES
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }
}