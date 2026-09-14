<?php

namespace App\Models\Traits;

use App\Models\Traza;
use App\Observers\TrazaObserver;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTrazas
{
    /**
     * Boot del trait: registra el observer automáticamente
     */
    protected static function bootHasTrazas(): void
    {
        static::observe(TrazaObserver::class);
    }

    /**
     * Relación polimórfica con trazas
     */
    public function trazas(): MorphMany
    {
        return $this->morphMany(Traza::class, 'trazable');
    }

    /**
     * Devuelve los campos que no se deben guardar en la traza
     * Se puede sobrescribir en el modelo con $trazaHidden
     */
    public function getTrazaHidden(): array
    {
        return property_exists($this, 'trazaHidden') && is_array($this->trazaHidden)
            ? $this->trazaHidden
            : [
                'password',
                'remember_token',
                'api_token',
                'two_factor_secret',
                'two_factor_recovery_codes',
                'created_at',
                'updated_at',
                'email_verified_at',
            ];
    }

    /**
     * Limpia datos sensibles antes de guardarlos en la traza
     */
    public function cleanTrazaData(array $data): array
    {
        return collect($data)
            ->except($this->getTrazaHidden())
            ->toArray();
    }

    /**
     * Agrega una nueva traza al modelo
     */
    public function addTraza(string $operacion, array $data = [], ?int $userId = null): Traza
    {
        return $this->trazas()->create([
            'user_id'   => $userId ?? auth()->id(),
            'operacion' => $operacion,
            'data'      => $this->cleanTrazaData($data),
            'ip_str'    => request()?->ip() ?? '0.0.0.0', // IP del usuario o 0.0.0.0
        ]);
    }

    /**
     * Determina si el modelo incluye snapshot completo al eliminar
     * Se puede sobrescribir en el modelo con $includeAllOnDelete
     */
    public function includeAllOnDelete(): bool
    {
        return property_exists($this, 'includeAllOnDelete')
            ? (bool) $this->includeAllOnDelete
            : false;
    }
}
