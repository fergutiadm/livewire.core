<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use App\Modules\Inventario\Atributo\Models\AtributoValor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductoVariante extends Model
{
    use HasTrazas;

    protected $table = 'producto_variantes';

    protected $fillable = [
        'producto_id',
        'clave_combinacion',
        'nombre',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function atributosValores(): BelongsToMany
    {
        return $this->belongsToMany(
            AtributoValor::class,
            'producto_variantes_atributos_valores',
            'producto_variante_id',
            'atributo_valor_id'
        );
    }
}
