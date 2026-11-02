<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class AyudaCategoria extends Model
{
    use HasTrazas;
    
    protected $table = 'ayuda_categorias';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'orden_visual',
        'activa',
    ];

    protected $casts = [
        'orden_visual' => 'integer',
        'activa' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($ayudaCategoria) {
            // Orden visual automático
            if ($ayudaCategoria->orden_visual === null || $ayudaCategoria->orden_visual === 0) {
                $ayudaCategoria->orden_visual =
                    self::max('orden_visual') + 1;
            }
        });
    }

    public function articulos(): HasMany
    {
        return $this->hasMany(
            AyudaArticulo::class,
            'categoria_id'
        );
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden_visual')
            ->orderBy('nombre');
    }
}
