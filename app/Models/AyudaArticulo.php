<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AyudaArticulo extends Model
{
    use HasTrazas;
    
    protected $table = 'ayuda_articulos';

    protected $fillable = [
        'categoria_id',
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'contexto',
        'orden_visual',
        'publicado',
    ];

    protected $casts = [
        'categoria_id' => 'integer',
        'orden_visual' => 'integer',
        'publicado' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($ayudaArticulo) {
            // Orden visual automático
            if ($ayudaArticulo->orden_visual === null) {
                $ayudaArticulo->orden_visual =
                    (self::max('orden_visual') ?? -1) + 1;
            }
        });
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            AyudaCategoria::class,
            'categoria_id'
        );
    }

    public function scopePublicados($query)
    {
        return $query
            ->where('publicado', true)
            ->whereHas('categoria', function ($query) {
                $query->where('activa', true);
            });
    }

    public function scopePorContexto($query, string $contexto)
    {
        return $query->where('contexto', $contexto);
    }

    public function scopeOrdenados($query)
    {
        return $query
            ->orderBy('orden_visual')
            ->orderBy('titulo');
    }
}
