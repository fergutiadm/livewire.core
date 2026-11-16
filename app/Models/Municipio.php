<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Municipio extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'municipios';

    protected $fillable = [
        'provincia_id',
        'nombre',
        'slug',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * RELACIONES
     */
    public function provincia(): BelongsTo
    {
        return $this->belongsTo(
            Provincia::class,
            'provincia_id'
        );
    }

    public function bancos(): HasMany
    {
        return $this->hasMany(
            Banco::class,
            'municipio_id'
        );
    }

    public function sucursales(): HasMany
    {
        return $this->hasMany(
            BancoSucursal::class,
            'municipio_id'
        );
    }

    /**
     * SCOPES
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorProvincia($query, int $provinciaId)
    {
        return $query->where('provincia_id', $provinciaId);
    }
}