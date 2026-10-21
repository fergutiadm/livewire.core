<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Moneda;

class MonedaService
{
    public const CACHE_KEY = 'ui.monedas';
    public const CACHE_TTL = 300;
    public const CODIGO_USD = 'USD';
    public const CODIGO_CUP = 'CUP';

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

    public function tasaPorId(int $monedaId): ?float
    {
        if (!Schema::hasTable('monedas')) {
            return null;
        }

        return Moneda::query()
            ->whereKey($monedaId)
            ->Activas()
            ->value('tasa_cambio');
    }

    public function tasaPorCodigo(string $codigo): ?float
    {
        if (!Schema::hasTable('monedas')) {
            return null;
        }

        return Moneda::query()
            ->whereRaw('UPPER(codigo) = ?', [strtoupper($codigo)])
            ->Activas()
            ->value('tasa_cambio');
    }

    public function decorateCodigo(string $codigo): array
    {
        if (!Schema::hasTable('monedas')) {
            return [];
        }

        // Buscamos el registro único de la moneda por su código
        $moneda = Moneda::query()
            ->whereRaw('UPPER(codigo) = ?', [strtoupper($codigo)])
            ->activas() // Usamos el scope en minúsculas corregido anteriormente
            ->first(['codigo', 'simbolo', 'color_bg', 'color_text']);

        if (!$moneda) {
            return [];
        }

        return [
            'codigo'      => strtoupper($moneda->codigo),
            'simbolo'     => strtoupper($moneda->simbolo),
            'color_bg'    => $moneda->color_bg ?: 'bg-white',
            'color_text'  => $moneda->color_text ?: 'text-black',
        ];
    }


    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
