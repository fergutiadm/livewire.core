<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Banco extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'bancos';

    protected $fillable = [
        'nombre',
        'alias',
        'codigo_swift',
        'telefonos',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * RELACIONES
     */

    public function sucursales(): HasMany
    {
        return $this->hasMany(
            BancoSucursal::class,
            'banco_id'
        );
    }

    /**
     * SCOPES
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
