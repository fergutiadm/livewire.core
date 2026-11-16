<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Local extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $table = 'locales';

    protected $fillable = [
        'nombre',
        'descripcion',
        'es_principal',
        'tipo_local_id'
    ];

    /* =====================
     |  RELACIONES
     ===================== */
    public function tipoLocal(): BelongsTo
    {
        return $this->belongsTo(TipoLocal::class);
    }

    public function monedaFondos(): HasMany
    {
        return $this->hasMany(MonedaFondo::class, 'local_id');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function periodosContables(): HasMany
    {
        return $this->hasMany(PeriodoContable::class);
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(Inventario::class);
    }

    public function origenes(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'origen');
    }

    public function destinos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'destino');
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }

    public function tiposGastos(): HasMany
    {
        return $this->hasMany(TipoGasto::class);
    }

    public function Cajas(): HasMany
    {
        return $this->hasMany(Caja::class);
    }

    public function CierresCaja(): HasMany
    {
        return $this->hasMany(CierreCaja::class);
    }

    /* =====================
     |  SCOPES
     ===================== */

    public function scopeEsPrincipal($query): Builder
    {
        return $query->where('es_principal', true);
    }

    public function scopeOperativos(Builder $query): Builder
    {
        return $query->whereHas('tipoLocal', function (Builder $q) {
            $q->where('codigo', 'operativo');
        });
    }

    public function scopeAlmacenes(Builder $query): Builder
    {
        return $query->whereHas('tipoLocal', function (Builder $q) {
            $q->where('codigo', 'almacen');
        });
    }
}
