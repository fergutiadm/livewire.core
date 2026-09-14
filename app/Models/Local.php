<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'descripcion'
    ];

    /* =====================
     |  RELACIONES
     ===================== */

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
}
