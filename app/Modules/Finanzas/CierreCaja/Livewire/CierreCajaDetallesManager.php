<?php

namespace App\Modules\Finanzas\CierreCaja\Livewire;

use App\Models\CierreCaja;
use App\Models\CierreCajaMoneda;
use App\Models\CierreCajaMonedaDetalle;
use App\Models\Denominacion;
use App\Models\Moneda;
use App\Modules\Finanzas\CierreCaja\Actions\SaveCierreCajaMonedaDetallesAction;
use App\Modules\Finanzas\CierreCaja\DTOs\SaveCierreCajaMonedaDetallesDTO;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CierreCajaDetallesManager extends Component
{
    public ?int $cierreCajaMonedaId = null;
    public string $montoEsperado = '';

    public string $cajaNombre = '';

    public ?int $monedaId = null;
    public string $monedaCodigo = '';
    public string $monedaColorBg = '';
    public string $monedaColorText = '';

    public bool $show = false;

    /**
     * Matriz de trabajo actual.
     *
     * [
     *     atributoId => [
     *         'atributo' => 'Color',
     *     ],
     * ]
     */
    public array $cards = [];

    /**
     * Snapshot tomado al cargar desde BD.
     *
     * Se utiliza como referencia para diagnosticar cambios.
     */
    public array $originalCards = [];

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(?int $cierreCajaMonedaId = null): void
    {
        if ($cierreCajaMonedaId !== null) {
            $this->cargarCierreCajaMonedaDetalles($cierreCajaMonedaId);
        }
    }

    /**

    * ---
    * Determinar si existen cambios pendientes
    * ---

    */
    public function hayCambios(): bool
    {
        return $this->normalizarCards($this->cards)
            !== $this->normalizarCards($this->originalCards);
    }

    /**

    * ---
    * Normalizar matriz de detalles
    * ---
    *
    */
    
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

        $cantidad = $this->cards[$denominacionId]['cantidad_billetes'] ?? 0;

        /**
         * Permitir que cantidad lo dejen vacio
         */
        if(empty($cantidad)){
            $this->resetValidation("cards.{$denominacionId}.cantidad_billetes");
        
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


    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

        
    public function guardar(): void
    {
        if (!$this->cierreCajaMonedaId) {
            return;
        }

        $this->validate([
            'cards' => 'array',
            'cards.*.valor_denominacion' => 'required|numeric|gt:0',
            'cards.*.cantidad_billetes' => 'required|integer|min:0',
        ]);

        $cierreCajaMoneda = CierreCajaMoneda::findOrFail(
            $this->cierreCajaMonedaId
        );

        if (!$this->hayCambios()) {
            return;
        }

        $validarTotal = bccomp(
            $this->montoEsperado,
            $this->montoTotalContado(),
            2
        ) === 0;

        if(!$validarTotal){
            $this->dispatch('livewire:alert', [
                'message' => 'No coincide Monto Esperado con Total Contado',
                'type' => 'error',
            ]);
            return;
        }
        

        $dto = new SaveCierreCajaMonedaDetallesDTO(
            cierreCajaMonedaId: $this->cierreCajaMonedaId,
            cards: $this->cards,
        );

        app(SaveCierreCajaMonedaDetallesAction::class)->execute($dto);

        $this->loadDetalles();

        $this->dispatch('livewire:alert', [
            'message' => 'Detalles guardados correctamente',
            'type' => 'success',
        ]);

        $this->show = false;
    }


    /*
    |--------------------------------------------------------------------------
    | Cargar detalles explícitamente
    |--------------------------------------------------------------------------
    */

    
    #[On('cierre-caja-moneda-detalles-cargar')]
    public function cargarCierreCajaMonedaDetalles(
        ?int $cierreCajaMonedaId = null
    ): void {
        $this->cards = [];
        $this->originalCards = [];

        if (!$cierreCajaMonedaId) {
            $this->cerrar();
            return;
        }

        $this->cierreCajaMonedaId = $cierreCajaMonedaId;

        $cierreCajaMoneda = CierreCajaMoneda::with([
            'cierre_caja.caja',
            'moneda',
        ])->findOrFail($cierreCajaMonedaId);

        $this->cajaNombre =
            $cierreCajaMoneda->cierre_caja?->caja?->nombre ?? '';

        $this->montoEsperado = (string) (
            $cierreCajaMoneda->monto_esperado ?? '0.00'
        );

        $moneda = $cierreCajaMoneda->moneda;

        $this->monedaId = $moneda?->id;
        $this->monedaCodigo = $moneda?->codigo ?? '';
        $this->monedaColorBg = $moneda?->color_bg ?? '';
        $this->monedaColorText = $moneda?->color_text ?? '';

        $this->loadDetalles();

        $this->show = true;
    }

    protected function loadDetalles(): void
    {
        $this->cards = [];

        if (!$this->cierreCajaMonedaId || !$this->monedaId) {
            return;
        }

        $denominaciones = Denominacion::query()
            ->activas()
            ->where('moneda_id', $this->monedaId)
            ->orderBy('valor')
            ->get();

        foreach ($denominaciones as $denominacion) {
            $id = (int) $denominacion->id;

            $this->cards[$id] = [
                'denominacion' => (string) $denominacion->valor,
                'valor_denominacion' => (string) $denominacion->valor,
                'cantidad_billetes' => '', //permitir empty
            ];

            $this->resetValidation("cards.{$denominacion->id}.cantidad_billetes");
        }

        $detalles = CierreCajaMonedaDetalle::query()
            ->where('cierre_caja_moneda_id', $this->cierreCajaMonedaId)
            ->orderBy('valor_denominacion')
            ->get();

        foreach ($detalles as $detalle) {
            $id = (int) $detalle->denominacion_id;

            // Conservar detalles históricos aunque su denominación
            // ya no esté activa en el catálogo.
            $valor = (string) $detalle->valor_denominacion;

            $this->cards[$id] = [
                'denominacion' => $valor,
                'valor_denominacion' => $valor,
                'cantidad_billetes' => (int) $detalle->cantidad_billetes,
            ];
        }

        ksort($this->cards);

        $this->originalCards = $this->cards;
    }


    /*
    |--------------------------------------------------------------------------
    | Cerrar modal
    |--------------------------------------------------------------------------
    */

    public function cerrar(): void
    {
        $this->show = false;

        $this->cierreCajaMonedaId = null;
        $this->monedaId = null;
        $this->cajaNombre = '';
        $this->monedaCodigo = '';
        $this->monedaColorBg = '';
        $this->monedaColorText = '';
        $this->montoEsperado = '';

        $this->cards = [];
        $this->originalCards = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $monedas = Moneda::ParaSelect()->get();
        return view(
            'modules.finanzas.cierre-caja.detalles-manager',
            ['monedas' => $monedas]
        );
    }
}