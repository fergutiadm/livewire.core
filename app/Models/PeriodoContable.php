<?php

namespace App\Models;

use App\Models\Traits\HasTrazas;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PeriodoContable extends Model
{
    use HasFactory, HasTrazas;

    protected bool $includeAllOnDelete = true;

    protected $table = 'periodos_contables';

    protected $fillable = [
        'local_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'cerrado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
        'cerrado'      => 'boolean',
    ];

    /* =====================================================
     |  DOMINIO
     ===================================================== */

    public function cerrar(): void
    {
        if ($this->cerrado) {
            return;
        }

        $this->update([
            'activo'  => false,
            'cerrado' => true,
        ]);
    }

    public static function cerrarYAbrirNuevo(self $periodoActual, array $nuevoPeriodo = []): self
    {
        return DB::transaction(function () use ($periodoActual, $nuevoPeriodo) {

            $periodoActual->cerrar();

            return $periodoActual->abrirSiguiente($nuevoPeriodo);
        });
    }

    public function abrirSiguiente(array $data = []): self
    {
        return static::create(array_merge([
            'local_id'      => $this->local_id,
            'nombre'        => 'Periodo ' . now()->format('Y-m'),
            'fecha_inicio'  => $this->fecha_fin->addDay(),
            'fecha_fin'     => $this->fecha_fin->addMonth(),
            'activo'        => true,
            'cerrado'       => false,
        ], $data));
    }


    /* =====================================================
     |  RELACIONES
     ===================================================== */

    public function local(): BelongsTo
    {
        return $this->belongsTo(Local::class);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    /* =====================================================
     |  SCOPES (mínimos, reutilizables)
     ===================================================== */

    public function scopeDeLocal(Builder $q, int $localId): Builder
    {
        return $q->where('local_id', $localId);
    }

    public function scopeVigente(Builder $q): Builder
    {
        return $q->where('activo', true)
                 ->where('cerrado', false);
    }

    public function scopeMasReciente(Builder $q): Builder
    {
        return $q->orderByDesc('fecha_inicio');
    }

    /* =====================================================
     |  QUERIES DE DOMINIO (LOS IMPORTANTES)
     ===================================================== */

    public static function vigenteActual(int $localId): ?self
    {
        return static::query()
            ->deLocal($localId)
            ->vigente()
            ->masReciente()
            ->first();
    }

    /**
     * Alias semántico (opcional pero muy legible en UI)
     */
    public static function actualDeLocal(int $localId): ?self
    {
        return static::vigenteActual($localId);
    }

    public static function ultimoDeLocal(int $localId): ?self
    {
        return static::deLocal($localId)
            ->orderByDesc('fecha_fin')
            ->first();
    }


    /* =====================================================
     |  UI HELPERS
     ===================================================== */

    public function getEstadoBadgeAttribute(): array
    {
        if ($this->cerrado) {
            return ['label' => 'Cerrado', 'class' => 'bg-gray-100 text-gray-700 ring-gray-200'];
        }

        if ($this->activo) {
            return ['label' => 'Activo', 'class' => 'bg-emerald-50 text-emerald-700 ring-emerald-200'];
        }

        return ['label' => 'Inactivo', 'class' => 'bg-slate-100 text-slate-700 ring-slate-200'];
    }

    public function dias(): int
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin) + 1;
    }

    /* =====================================================
     |  HOOKS DE INTEGRIDAD
     ===================================================== */

     protected static function booted()
     {
         static::saving(function (PeriodoContable $periodo) {

             // Regla 1:
             // Nunca permitir cruces de fechas.
             $periodo->validarRangoSinCruces();

             // Regla 2:
             // Solo un período vigente por local.
             if (! $periodo->activo || $periodo->cerrado) {
                 return;
             }

             $existeOtro = static::query()
                 ->deLocal($periodo->local_id)
                 ->vigente()
                 ->when(
                     $periodo->exists,
                     fn ($q) => $q->where('id', '!=', $periodo->id)
                 )
                 ->exists();

             if ($existeOtro) {
                 throw new \LogicException(
                     'Ya existe un período contable activo para este local.'
                 );
             }
         });
     }

     public function validarRangoSinCruces(): void
    {
        $existeCruce = static::query()
            ->deLocal($this->local_id)
            ->where('fecha_inicio', '<=', $this->fecha_fin)
            ->where('fecha_fin', '>=', $this->fecha_inicio)
            ->when(
                $this->exists,
                fn ($q) => $q->where('id', '!=', $this->id)
            )
            ->exists();

        if ($existeCruce) {
            throw new \LogicException(
                'El período contable se cruza con otro período existente para este local.'
            );
        }
    }
}
