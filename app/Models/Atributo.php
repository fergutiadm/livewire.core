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
                            'nombre',
                            'descripcion',
                            'orden_visual',
                            'color_bg',
                            'color_text',
                          ];


    protected static function booted()
    {
        static::creating(function ($atributo) {
            if ($atributo->orden_visual === null) {
                $atributo->orden_visual =
                    self::max('orden_visual') + 1;
            }
        });
    }

    /* =====================
    |  RELACIONES
    ===================== */

    public function valores(): HasMany
    {
        return $this->hasMany(AtributoValor::class);
    }
}
