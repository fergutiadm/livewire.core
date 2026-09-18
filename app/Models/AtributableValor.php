<?php

namespace App\Modules\Inventario\Atributo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AtributoValor extends Model
{
    use HasFactory;

    protected $table = 'atributos_valores';

    protected $fillable = [
        'atributo_id',
        'codigo',
        'nombre',
        'orden_visual',
        'activo',
    ];

    protected $casts = [
        'orden_visual' => 'integer',
        'activo' => 'boolean',
    ];

    public function atributo(): BelongsTo
    {
        return $this->belongsTo(Atributo::class);
    }

    // public function variantes(): BelongsToMany
    // {
    //     return $this->belongsToMany(
    //         ProductoVariante::class,
    //         'producto_variantes_atributos_valores',
    //         'atributo_valor_id',
    //         'producto_variante_id'
    //     );
    // }
}