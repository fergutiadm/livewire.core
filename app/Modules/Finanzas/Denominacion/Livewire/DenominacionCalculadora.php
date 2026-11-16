<?php

namespace App\Modules\Finanzas\Denominacion\Livewire;

use App\Models\Denominacion;
use App\Services\MonedaService;
use Livewire\Component;
use Livewire\Attributes\On;

class DenominacionCalculadora extends Component
{
    public string $montoEsperado = '';

    public ?int $monedaId = null;
    public string $monedaCodigo = '';
    public string $monedaColorBg = '';
    public string $monedaColorText = '';

    public bool $showModal = false;

    public array $monedas = [];

    /**
     * Tarjetas de la moneda actualmente seleccionada.
     */
    public array $cards = [];

    /**
     * Snapshot de las tarjetas de la moneda seleccionada.
     */
    public array $originalCards = [];

    /**
     * Tarjetas agrupadas por moneda.
     *
     * [
     *     monedaId => [
     *         denominacionId => [...]
     *     ]
     * ]
     */
    public array $cardsPorMoneda = [];

    /**
     * Estado original agrupado por moneda.
     */
    public array $originalCardsPorMoneda = [];

    public bool $monedaFija = false;

    public function mount(
        ?int $monedaId = null,
        bool $monedaFija = false
    ): void {
        $this->monedaFija = $monedaFija;

        $this->inicializar($monedaId);
    }

    #[On('calculadora-denominacion-abrir')]
    public function abrirParaMoneda(
        int $monedaId,
        string $montoEsperado = '',
        ?array $cards = []
    ): void {
        if (empty($this->monedas)) {
            $this->monedas = app(MonedaService::class)->listForDisplay();
        }

        $moneda = collect($this->monedas)
            ->firstWhere('id', $monedaId);

        if (!$moneda) {
            return;
        }

        $this->monedaFija = true;

        // La apertura representa una nueva operación.
        $this->cards = [];
        $this->originalCards = [];
        $this->cardsPorMoneda = [];
        $this->originalCardsPorMoneda = [];

        // Inicilicamos cards si lo envian como parametro
        if($cards)
        {
            $this->cards = $cards;
            $this->originalCards = $cards;
            $this->cardsPorMoneda[$monedaId] = $cards;
            $this->originalCardsPorMoneda[$monedaId] = $cards;
        }

        $this->montoEsperado = $montoEsperado;

        $this->monedaId = null;

        $this->cambiarMoneda($monedaId);

        $this->showModal = true;
    }

    protected function inicializar(?int $monedaId = null): void
    {
        $this->monedas = app(MonedaService::class)->listForDisplay();

        if (empty($this->monedas)) {
            return;
        }

        $monedaSeleccionada = collect($this->monedas)
            ->firstWhere('id', $monedaId);

        if (!$monedaSeleccionada) {
            $monedaSeleccionada = collect($this->monedas)
                ->first(fn (array $moneda) => $moneda['es_principal'])
                ?? $this->monedas[0];
        }

        $this->cambiarMoneda((int) $monedaSeleccionada['id']);
    }

    public function openModal(): void
    {
        if (empty($this->monedas)) {
            $this->inicializar();
        } elseif (!$this->monedaId) {
            $this->inicializar();
        }

        $this->showModal = true;
    }

    public function cambiarMoneda(int $monedaId): void
    {
            if (
                $this->monedaFija
                && $this->monedaId !== null
                && $this->monedaId !== $monedaId
            ) {
                return;
            }

        $moneda = collect($this->monedas)
            ->firstWhere('id', $monedaId);

        // Solo se permite seleccionar monedas disponibles.
        if (!$moneda) {
            return;
        }

        // Conservar el estado de la moneda que se abandona.
        if ($this->monedaId !== null) {
            $this->cardsPorMoneda[$this->monedaId] = $this->cards;
        }

        $this->monedaId = $monedaId;

        $this->monedaCodigo = $moneda['codigo'];
        $this->monedaColorBg = $moneda['color_bg'];
        $this->monedaColorText = $moneda['color_text'];

        // Si ya se cargó anteriormente, recuperar sus tarjetas.
        if (array_key_exists($monedaId, $this->cardsPorMoneda)) {
            $this->cards = $this->cardsPorMoneda[$monedaId];
        } else {
            // Primera selección de esta moneda: cargar desde BD.
            $this->cards = $this->cargarDenominaciones($monedaId);

            $this->cardsPorMoneda[$monedaId] = $this->cards;

            $this->originalCardsPorMoneda[$monedaId] = $this->cards;
        }

        $this->originalCards = $this->originalCardsPorMoneda[$monedaId] ?? [];

        $this->limpiarValidaciones();
    }

    protected function cargarDenominaciones(int $monedaId): array
    {
        $cards = [];

        $denominaciones = Denominacion::query()
            ->activas()
            ->where('moneda_id', $monedaId)
            ->orderBy('valor')
            ->get();

        foreach ($denominaciones as $denominacion) {
            $id = (int) $denominacion->id;

            $cards[$id] = [
                'denominacion'       => (string) $denominacion->valor,
                'color_bg'           => (string) $denominacion->color_bg,
                'color_text'         => (string) $denominacion->color_text,
                'valor_denominacion' => (string) $denominacion->valor,
                'cantidad_billetes'  => '',
            ];
        }

        ksort($cards);

        return $cards;
    }

    public function hayCambios(): bool
    {
        $cardsActualesPorMoneda = $this->cardsPorMoneda;

        // Incluir siempre el estado más reciente de la moneda seleccionada.
        if ($this->monedaId !== null) {
            $cardsActualesPorMoneda[$this->monedaId] = $this->cards;
        }

        return $this->normalizarCardsPorMoneda($cardsActualesPorMoneda)
            !== $this->normalizarCardsPorMoneda($this->originalCardsPorMoneda);
    }

    protected function normalizarCardsPorMoneda(array $cardsPorMoneda): array
    {
        $normalizados = [];

        foreach ($cardsPorMoneda as $monedaId => $cards) {
            $normalizados[(int) $monedaId] = $this->normalizarCards($cards);
        }

        ksort($normalizados);

        return $normalizados;
    }

    protected function normalizarCards(array $cards): array
    {
        $normalizados = [];

        foreach ($cards as $denominacionId => $card) {
            $normalizados[(int) $denominacionId] = [
                'valor_denominacion' => (string) (
                    $card['valor_denominacion'] ?? '0'
                ),
                'cantidad_billetes' => (int) (
                    $card['cantidad_billetes'] ?? 0
                ),
            ];
        }

        ksort($normalizados);

        return $normalizados;
    }

    public function actualizarCantidadBilletes(int $denominacionId): void
    {
        if (!isset($this->cards[$denominacionId])) {
            return;
        }

        $this->resetValidation('cards');

        $cantidad = $this->cards[$denominacionId]['cantidad_billetes'] ?? 0;

        if (empty($cantidad)) {
            $this->cards[$denominacionId]['cantidad_billetes'] = '';
            $this->resetValidation(
                "cards.{$denominacionId}.cantidad_billetes"
            );

            return;
        }

        if (
            filter_var($cantidad, FILTER_VALIDATE_INT) === false
            || (int) $cantidad < 0
        ) {
            $this->addError(
                "cards.{$denominacionId}.cantidad_billetes",
                'La cantidad debe ser un entero mayor o igual a cero.'
            );

            return;
        }

        $this->cards[$denominacionId]['cantidad_billetes'] = (int) $cantidad;

        $this->resetValidation(
            "cards.{$denominacionId}.cantidad_billetes"
        );
    }

    protected function calcularImporteDenominacion(array $card): string
    {
        return bcmul(
            (string) ($card['valor_denominacion'] ?? '0'),
            (string) ((int) ($card['cantidad_billetes'] ?? 0)),
            2
        );
    }

    public function importeDenominacion(int $denominacionId): string
    {
        if (!isset($this->cards[$denominacionId])) {
            return '0.00';
        }

        return $this->calcularImporteDenominacion(
            $this->cards[$denominacionId]
        );
    }

    public function montoTotalContado(): string
    {
        $total = '0.00';

        foreach ($this->cards as $card) {
            $total = bcadd(
                $total,
                $this->calcularImporteDenominacion($card),
                2
            );
        }

        return $total;
    }

    public function guardar(): void
    {
        if ($this->monedaId === null) {
            return;
        }

        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }

        $total = $this->montoTotalContado();

        if (bccomp($total, '0.00', 2) <= 0) {
            $this->addError(
                'cards',
                'Debe indicar al menos una cantidad de billetes.'
            );

            return;
        }

        $this->dispatch(
            'calculadora-denominacion-aplicada',
            monedaId: $this->monedaId,
            total: $total,
            cards: $this->cards
        );

        $this->cerrar();
    }

    protected function limpiarValidaciones(): void
    {
        $this->resetValidation();
    }

    public function cerrar(): void
    {
        $this->showModal = false;

        $this->monedaId = null;
        $this->monedaCodigo = '';
        $this->monedaColorBg = '';
        $this->monedaColorText = '';
        $this->montoEsperado = '';

        $this->cards = [];
        $this->originalCards = [];

        $this->cardsPorMoneda = [];
        $this->originalCardsPorMoneda = [];

        $this->resetValidation();
    }

    public function render()
    {
        return view('modules.finanzas.denominacion.calculadora');
    }
}
