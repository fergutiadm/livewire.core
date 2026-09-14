<?php

namespace App\Http\Livewire\Currency;

use Livewire\Component;
use App\Models\Moneda;
use Illuminate\Support\Collection;

class RatesBar extends Component
{
    public Collection $items;
    public string $variant;
    public int $decimals;
    public bool $showBoth;

    public array $primary = [];
    public Collection $others;

    protected $listeners = [
        'monedasActualizadas' => 'loadMonedas', // escucha el evento
    ];


    public function mount(string $variant = 'stacked', int $decimals = 4, bool $showBoth = false)
    {
        $this->variant = $variant;
        $this->decimals = $decimals;
        $this->showBoth = $showBoth;

        $this->loadMonedas();
    }

    public function loadMonedas(): void
    {
        // Traemos las monedas activas
        $monedas = Moneda::where('activa', true)->orderByDesc('es_principal')->get();

        // Calculamos eq_per_primary
        $primary = $monedas->firstWhere('es_principal', true);
        $rate = $primary->tasa_cambio ?? 1;
        $rate = $rate <= 0 ? 1.0 : $rate; // evitar /0 o negativos

        $items = $monedas->map(function ($m) use ($rate) {
            $tasa_cambio = $m->tasa_cambio ?? 1;
            $tasa_cambio = $tasa_cambio <= 0 ? 1.0 : $tasa_cambio; // evitar /0 o negativos
            return [
                'id' => $m->id,
                'codigo' => $m->codigo,
                'simbolo' => $m->simbolo,
                'tasa_cambio' => $m->tasa_cambio,
                'eq_per_primary' => $m->tasa_cambio / ($rate ?: 1),
                'inverse' => 1.0 / $tasa_cambio,
                'color_bg' => $m->color_bg ?? 'bg-white',
                'color_text' => $m->color_text ?? 'text-black',
                'es_principal' => $m->es_principal,
            ];
        });

        $this->items = $items;
        $this->primary = $items->firstWhere('es_principal', true) ?? [];
        $this->others = $items->filter(fn($i) => !$i['es_principal']);
    }

    public function render()
    {
        return view('livewire.currency.rates-bar');
    }
}
