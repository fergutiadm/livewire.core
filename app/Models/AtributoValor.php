<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtributoValor extends Model
{
    use HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $table = 'atributos_valores';

    protected $fillable = ['atributo_id', 'valor', 'descripcion', 'orden_visual'];

    public function categorias()
    {
        return $this->morphedByMany(
            Categoria::class,
            'atributable',
            'atributables_valores'
        );
    }

    public function productos()
    {
        return $this->morphedByMany(
            Producto::class,
            'atributable',
            'atributables_valores'
        );
    }

    /* =====================
     |  RELACIONES
     ===================== */
    public function atributo(): BelongsTo
    {
        return $this->belongsTo(Atributo::class);
    }
}
