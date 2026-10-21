<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GastoPagoDesglose extends Model
{
    use HasFactory, HasTrazas;

    protected $table = 'gasto_pago_desgloses';

    protected bool $includeAllOnDelete = true;

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'gasto_pago_id',
        'moneda_id',
        'cantidad',
        'tasa_cambio',
        'metodo',
        'tarjeta_magnetica_id',
        'numero_tarjeta',
    ];

    protected function cantidad(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null
                ? number_format((float) $value, 2, '.', '')
                : null,
        );
    }

    protected function tasaCambio(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null
                ? number_format((float) $value, 2, '.', '')
                : null,
        );
    }

    /**
     * Importe del desglose expresado en CUP.
     *
     * cantidad × tasa de cambio histórica.
     */
    protected function importeCup(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $cantidad = (float) ($attributes['cantidad'] ?? 0);
                $tasa = (float) ($attributes['tasa_cambio'] ?? 0);

                return number_format(
                    $cantidad * $tasa,
                    2,
                    '.',
                    ''
                );
            }
        );
    }

    public function gastoPago(): BelongsTo
    {
        return $this->belongsTo(GastoPago::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function tarjetaMagnetica(): BelongsTo
    {
        return $this->belongsTo(TarjetaMagnetica::class);
    }
}