<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoUnidad extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'producto_unidades';

    protected $fillable = [
        'producto_id',
        'unidad_medida_id',
        'factor_a_base',
        'permite_fraccion',
        'activo',
    ];

    protected $casts = [
        'factor_a_base' => 'decimal:10',
        'permite_fraccion' => 'boolean',
        'activo' => 'boolean',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(
            UnidadMedida::class,
            'unidad_medida_id'
        );
    }
}
