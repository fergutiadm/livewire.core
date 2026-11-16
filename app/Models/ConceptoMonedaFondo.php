<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConceptoMonedaFondo extends Model
{
    use HasFactory, HasTrazas;

    public const TIPO_ESTANDAR     = 'estandar';
    public const TIPO_OPERACIONAL  = 'operacional';

    protected $table = 'conceptos_moneda_fondo';

    protected $fillable = [
        'nombre',
        'tipo',
        'slug',
        'entrada_salida',
        'activo',
    ];

    protected $casts = [
        'entrada_salida' => 'boolean',
        'activo' => 'boolean',
    ];

    public function monedaFondos(): HasMany
    {
        return $this->hasMany(MonedaFondo::class, 'concepto_id');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderBy('nombre');
    }
}