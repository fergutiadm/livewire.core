<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class TipoGasto extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $table = 'tipos_gastos';    

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'local_id',
        'nombre',
        'color_bg',
        'color_text',
        'descripcion',
        'fijo',
        'activo',
    ];

    /* =====================
     |  RELACIONES
     ===================== */

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    /* =====================================================
     |  SCOPES (mínimos, reutilizables)
     ===================================================== */

    public function scopeActivos(Builder $q): Builder
    {
        return $q->where('activo', true);
    }
}
