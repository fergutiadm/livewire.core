<?php

namespace App\View\Components\Currency;

use Illuminate\View\Component;
use Illuminate\Support\Collection;

class RatesBar extends Component
{
    public Collection $items;
    public string $codigoPrincipal;

    public function __construct(
        Collection $currencies,
        string $codigoPrincipal,
        public int $decimals = 4,
        public bool $showBoth = false,
        public string $variant = 'stacked',
    ) {
        $this->items = $currencies->values();
        $this->codigoPrincipal = $codigoPrincipal;
    }

    public function render()
    {
        return view('components.currency.rates-bar');
    }
}


