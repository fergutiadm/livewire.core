<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Moneda;

class MonedaService
{
    public const CACHE_KEY = 'ui.monedas';
    public const CACHE_TTL = 300;

    public function listForDisplay(): array
    {
        if (!Schema::hasTable('monedas')) {
            return [];
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $rows = Moneda::query()
                ->where('activa', true)
                ->orderByDesc('es_principal')
                ->get()
                ->map(fn ($m) => [
                    'codigo'        => strtoupper($m->codigo),
                    'tasa_cambio'   => (float) $m->tasa_cambio,
                    'es_principal'  => (bool) $m->es_principal,
                    'nombre'        => $m->nombre,
                    'simbolo'       => $m->simbolo ?? '',
                    'color_bg'      => $m->color_bg ?: 'bg-white',
                    'color_text'    => $m->color_text ?: 'text-black',
                ]);

            $primary = $rows->firstWhere('es_principal', true) ?? $rows->first();
            $rate = $primary['tasa_cambio'] ?? 1;

            return $rows->map(fn ($m) => [
                ...$m,
                'eq_per_primary' => $m['tasa_cambio'] / $rate,
            ])->toArray();
        });
    }

    public function codigoPrincipal(): string
    {
        return collect($this->listForDisplay())
            ->firstWhere('es_principal', true)['codigo'] ?? '—';
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
