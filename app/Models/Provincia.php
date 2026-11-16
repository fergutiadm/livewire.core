<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provincia extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'provincias';

    protected $fillable = [
        'nombre',
        'slug',
        'activa',
        'orden',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /**
     * RELACIONES
     */
    public function municipios(): HasMany
    {
        return $this->hasMany(
            Municipio::class,
            'provincia_id'
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