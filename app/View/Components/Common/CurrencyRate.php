<?php

namespace App\View\Components\Common;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class CurrencyRate extends Component
{

    /** @var Collection<int, mixed> */
    public Collection $currencies;

    /**
     * @param  mixed $currencies   Collection|array
     */
    public function __construct(
        mixed $currencies = null,
    ) {
        $this->currencies = $this->normalizeCurrencies($currencies);
        $this->buildCard();
    }

    public function buildCard(): array
    {
        $list = collect($this->currencies);
        $primary = $list->firstWhere('is_primary', true)
                    ?: $list->firstWhere('rate_to_primary', 1)
                    ?: $list->first();

        $ordered = collect();
        if ($primary) {
            $ordered->push($primary);
            $ordered = $ordered->concat(
            $list->reject(fn($c) => (strtoupper($c->code ?? '') === strtoupper($primary->code ?? '')))
                ->sortBy(fn($c) => strtoupper($c->code ?? ''))
            );
        }

        return [];
    }

    private function normalizeCurrencies(mixed $currencies): Collection
    {
        if ($currencies instanceof Collection) return $currencies->values();
        if (is_array($currencies)) return collect($currencies)->values();
        return collect(); // vacío si no viene
    }
    /* ========================= Render ========================= */

    public function render(): View|Closure|string
    {
        return view('components.inventory.totales-inversion-complex', [
            // 'cards'       => $this->cards,
            // 'primaryCode' => $this->primaryCode,
            // 'raw'         => $this->raw,
            // 'title'       => $this->title,
        ]);
    }
}