<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $table = 'cajas';

    protected $fillable = [
        'local_id',
        'codigo',
        'nombre',
        'activa'
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    /* =====================
     |  ACCESSORS
     ===================== */
     
    /**
     * Devuelve el nombre del local
     * Buscar con la IA como hacer este metodo o accessor que debe obtener $this->local()->nombre
     */
    // protected function localNombre()
    // {
    //     $local = $this->local();
    //     return $local ? $local->nombre : '';
    // }

    /* =====================
     |  RELACIONES
     ===================== */

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function cierresCaja(): HasMany
    {
        return $this->hasMany(CierreCaja::class, 'caja_id');
    }

    /* =====================================================
     |  SCOPES (mínimos, reutilizables)
     ===================================================== */

    public function scopeDeLocal(Builder $q, int $localId): Builder
    {
        return $q->where('local_id', $localId);
    }


}
