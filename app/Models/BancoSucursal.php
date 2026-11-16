<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BancoSucursal extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'banco_sucursales';

    protected $fillable = [
        'banco_id',
        'municipio_id',
        'nombre',
        'codigo',
        'direccion',
        'telefonos',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function banco(): BelongsTo
    {
        return $this->belongsTo(
            Banco::class,
            'banco_id'
        );
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(
            Municipio::class,
            'municipio_id'
        );
    }

    public function cuentas(): HasMany
    {
        return $this->hasMany(
            CuentaBancaria::class,
            'sucursal_id'
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
