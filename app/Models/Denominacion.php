<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HasTrazas; // Incluido por si usas trazas en administración como en Local
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Denominacion extends Model
{
    use HasFactory, HasTrazas;

    /**
     * Tabla asociada en la base de datos.
     */
    protected $table = 'denominaciones';

    /**
     * Atributos asignables de forma masiva (Mass Assignment).
     */
    protected $fillable = [
        'moneda_id',
        'valor',
        'activa',
        'color_bg',
        'color_text',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos.
     */
    protected $casts = [
        'moneda_id' => 'integer',
        'activa'    => 'boolean',
    ];

    /* =========================================================================
     |  ACCESSORS & MUTATORS (Laravel 9/10)
     | ========================================================================= */

    /**
     * Fuerza al valor nominal a mostrar siempre exactamente 2 decimales limpios.
     * Ejemplo: 500 -> 500.00
     */
    protected function valor(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null ? number_format((float)$value, 2, '.', '') : null,
        );
    }

    /* =========================================================================
     |  RELACIONES
     | ========================================================================= */

    /**
     * Obtiene la moneda a la que pertenece esta denominación física.
     */
    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class, 'moneda_id');
    }

    public function cierreCajaDetalles(): HasMany
    {
        return $this->HasMany(CierreCajaMonedaDetalle::class);
    }

    /* =========================================================================
     |  SCOPES
     | ========================================================================= */

    /**
     * Obtiene la moneda a la que pertenece esta denominación física.
     */
    public function scopeActivas($query): Builder
    {
        return $query->where('activa', true);
    }
}
