<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTrazas;

use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gasto extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $table = 'gastos';    

    protected array $trazaHidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'local_id',
        'tipo_gasto_id',
        'periodo_contable_id',
        'especialista_id',
        'responsable_id',
        'operador_id',
        'cantidad',
        'valor',
        'moneda_id',
        'tasa_cambio',
        'tasa_cambio_usd',
        'fecha',
        'descripcion',
        'chequeado',
        'fecha_chequeado',
        'ip_var',
    ];

    /* =====================
     |  CASH
     ===================== */
    protected $casts = [
        'chequeado' => 'boolean',
        'tasa_cambio' => 'float',
    ];

     /**
     * Fuerza a la cantidad a mostrar siempre exactamente 2 decimales.
     */
    protected function cantidad(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null ? number_format((float)$value, 2, '.', '') : null,
        );
    }

    /**
     * Fuerza al valor a mostrar siempre exactamente 2 decimales.
     */
    protected function valor(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null ? number_format((float)$value, 2, '.', '') : null,
        );
    }

    /**
     * Fuerza a la tasa de cambio a mostrar siempre exactamente 2 decimales.
     */
    protected function tasaCambio(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value !== null ? number_format((float)$value, 2, '.', '') : null,
        );
    }

    /**
     * Campo virtual que calcula el importe en tiempo real.
     * Al usar el segundo parámetro ($attributes), accedemos a los valores numéricos
     * en bruto directo de la base de datos, evitando que number_format los convierta en strings.
     */
    protected function importe(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $cantidad = (float)($attributes['cantidad'] ?? 0);
                $valor    = (float)($attributes['valor'] ?? 0);

                $resultado = $cantidad * $valor;

                return number_format($resultado, 2, '.', '');
            }
        );
    }

    protected function importeBase(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $cantidad = (float) ($attributes['cantidad'] ?? 0);
                $valor = (float) ($attributes['valor'] ?? 0);
                $tasa = (float) ($attributes['tasa_cambio'] ?? 0);

                $resultado = $cantidad * $valor * $tasa;

                return number_format($resultado, 2, '.', '');
            }
        );
    }

    protected function importeUsd(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $cantidad = (float) ($attributes['cantidad'] ?? 0);
                $valor = (float) ($attributes['valor'] ?? 0);
                $tasa = (float) ($attributes['tasa_cambio'] ?? 0);
                $tasaUsd = (float) ($attributes['tasa_cambio_usd'] ?? 0);

                if ($tasaUsd <= 0) {
                    return '0.00';
                }

                $importeBase = $cantidad * $valor * $tasa;

                $resultado = $importeBase / $tasaUsd;

                return number_format($resultado, 2, '.', '');
            }
        );
    }

    /* =====================
     |  RELACIONES
     ===================== */

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function tipoGasto(): BelongsTo
    {
        return $this->belongsTo(TipoGasto::class);
    }

    public function periodoContable(): BelongsTo
    {
        return $this->belongsTo(PeriodoContable::class);
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class);
    }

    public function gastosPagos(): HasMany
    {
        return $this->hasMany(GastoPago::class);
    }

    public function pagosConfirmados(): HasMany
    {
        return $this->hasMany(GastoPago::class)
            ->where('estado', 'confirmed');
    }

    public function calcularPagadoCup(): float
    {
        return (float) $this->pagosConfirmados()
            ->with('desgloses')
            ->get()
            ->sum(function (GastoPago $pago) {
                return $pago->calcularImporteCup();
            });
    }

    public function calcularSaldoPendienteCup(): float
    {
        $total = (float) $this->importe_base;
        $pagado = $this->calcularPagadoCup();

        return max(0, $total - $pagado);
    }

    protected function saldoPendienteCup(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format(
                $this->calcularSaldoPendienteCup(),
                2,
                '.',
                ''
            )
        );
    }

    public function tienePagosConfirmados(): bool
    {
        return $this->pagosConfirmados()->exists();
    }
    
    public function puedeModificarDatosEconomicos(): bool
    {
        return ! $this->tienePagosConfirmados();
    }

    public function resumenEconomico(): array
    {
        $importe = (float) $this->importe_base;
        $pagado = $this->calcularPagadoCup();
        $saldo = max(0, $importe - $pagado);

        return [
            'importe_cup' => number_format($importe, 2, '.', ''),
            'pagado_cup' => number_format($pagado, 2, '.', ''),
            'saldo_cup' => number_format($saldo, 2, '.', ''),
        ];
    }
}
