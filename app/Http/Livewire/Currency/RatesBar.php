<?php

namespace App\Http\Livewire\Currency;

use App\Http\Livewire\Currency\Actions\SaveTasasCambioAction;
use App\Http\Livewire\Currency\DTOs\SaveTasasCambioDTO;
use Livewire\Component;
use App\Models\Moneda;
use App\Modules\Configuracion\SetupConfig\Services\SetupConfigService;
use App\Services\MonedaService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class RatesBar extends Component
{
    public Collection $items;
    public string $variant;
    public int $decimals;
    public bool $showBoth;
    public bool $shortView;

    public bool $showModal;

    public array $primary = [];
    public Collection $others;
    public array $originalOthers = [];

    protected $listeners = [
        'monedasActualizadas' => 'loadMonedas', // escucha el evento
    ];

    public function mount(
        SetupConfigService $config,
        string $variant = 'stacked',
        ?int $decimals = null,
        bool $showBoth = false,
        bool $shortView = false
    ): void {
        $this->variant = $variant;
        $this->decimals = $decimals
            ?? (int) ($config->get('decimales_tasa_cambio') ?? 0);

        $this->showBoth = $showBoth;
        $this->shortView = $shortView;
        $this->showModal = false;

        $this->loadMonedas();
    }

    #[On('actualizar-rates-bar')]
    public function actualizarRatesBar(
        SetupConfigService $config
    ): void {
        $this->decimals = (int) (
            $config->get('decimales_tasa_cambio') ?? 0
        );

        $this->loadMonedas();
    }

    /**

    * ---
    * Determinar si existen cambios pendientes
    * ---

    */
    public function hayCambios(): bool
    {
        foreach ($this->others as $id => $moneda) {
            if (
                (string) $moneda['tasa_cambio']
                !== (string) ($this->originalOthers[$id] ?? '')
            ) {
                return true;
            }
        }

        return false;
    }

    public function actualizarTasasCambio(int $otherId): void
    {
        if (!isset($this->others[$otherId])) {
            return;
        }

        $tasa_cambio = $this->others[$otherId]['tasa_cambio'] ?? 0;


        if (
            filter_var($tasa_cambio, FILTER_VALIDATE_INT) === false
            || (int) $tasa_cambio < 0
        ) {
            $this->addError(
                "others.{$otherId}.tasa_cambio",
                'La tasa de cambio debe ser un entero mayor o igual a cero.'
            );

            return;
        }

        $this->others[$otherId]['tasa_cambio'] = (int) $tasa_cambio;

        $this->resetValidation(
            "others.{$otherId}.tasa_cambio"
        );
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->loadMonedas();
    }

    public function guardarTasasCambio()
    {
        $this->validate([
            'others' => 'array',
            'others.*.tasa_cambio' => 'required|integer|min:0',
        ]);

        if (!$this->hayCambios()) {
            return;
        }

        $dto = new SaveTasasCambioDTO(
            monedas: $this->others->toArray(),
        );

        app(SaveTasasCambioAction::class)->execute($dto);
                        
        app(MonedaService::class)->forgetCache();

        $this->loadMonedas();

        $this->dispatch('livewire:alert', [
            'message' => 'Tasas de Cambio guardadas correctamente',
            'type' => 'success',
        ]);

        $this->closeModal();
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
        $this->others = $items
                ->reject(fn ($item) => $item['es_principal'])
                ->keyBy('id');
        $this->others->map(function($m){
            $this->resetValidation(
                "others.{$m['id']}.tasa_cambio"
            );
        });

        $this->originalOthers = $this->others
            ->map(fn ($item) => $item['tasa_cambio'])
            ->all();
    }

    public function render()
    {
        return view('livewire.currency.rates-bar');
    }
}
