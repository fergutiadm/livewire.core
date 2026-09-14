<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'unidades_medida';

    protected $fillable = [
        'unidad_dimension_id',
        'codigo',
        'nombre',
        'abreviatura',
        'tipo',
        'factor_base',
        'es_base_dimension',
        'activo',
    ];

    protected $casts = [
        'factor_base' => 'decimal:10',
        'es_base_dimension' => 'boolean',
        'activo' => 'boolean',
    ];

    public function dimension(): BelongsTo
    {
        return $this->belongsTo(
            UnidadDimension::class,
            'unidad_dimension_id'
        );
    }

    public function productosUnidades(): HasMany
    {
        return $this->hasMany(ProductoUnidad::class);
    }

    public function productosComoUnidadBase(): HasMany
    {
        return $this->hasMany(
            Producto::class,
            'unidad_medida_base_id'
        );
    }
}