<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atributo extends Model
{
    use HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'orden_visual',
        'color_bg',
        'color_text',
        'activo',
    ];

    protected $casts = [
        'orden_visual' => 'integer',
        'activo' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($atributo) {
            if ($atributo->orden_visual === null) {
                $atributo->orden_visual =
                    ((int) self::max('orden_visual')) + 1;
            }

            if ($atributo->activo === null) {
                $atributo->activo = true;
            }
        });
    }

    public function valores(): HasMany
    {
        return $this->hasMany(AtributoValor::class)
            ->orderBy('orden_visual');
    }
}