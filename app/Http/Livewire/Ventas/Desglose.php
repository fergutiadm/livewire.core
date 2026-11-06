<?php

namespace App\Http\Livewire\Ventas;

use App\Core\Context\FechaOperacionContext;
use App\Core\Context\LocalContext;
use App\Core\Context\PeriodoContext;
use App\Models\PeriodoContable;
use App\Services\MonedaService;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class Desglose extends Component
{
    private const CALC_SCALE = 10;

    public bool $showModal = false;

    public ?int $localId = null;
    public ?int $periodoContableId = null;
    public string $fechaOperacion = '';

    public array $primary = [];
    public array $others = [];
    public array $originalOthers = [];

    public string $valorVenta = '14500';
    public int $decimals = 2;

    public function mount(
        LocalContext $localContext,
        PeriodoContext $periodoContext,
        FechaOperacionContext $fechaOperacionContext
    ): void {
        $this->localId = $localContext->id();
        $this->periodoContableId = $periodoContext->id();
        $this->fechaOperacion = $fechaOperacionContext->fecha();

        $this->loadMonedas();
    }

    #[On('local-context-changed')]
    public function actualizarLocal(?int $localId = null): void
    {
        $this->localId = $localId ?? app(LocalContext::class)->id();
    }

    #[On('periodo-context-changed')]
    public function actualizarPeriodo(int $periodoId): void
    {
        $this->periodoContableId = $periodoId;
        $this->showModal = false;
    }

    #[On('fecha-operacion-context-changed')]
    public function actualizarFecha(string $fechaOperacion): void
    {
        $this->fechaOperacion = $fechaOperacion;
    }

    public function openModal(): void
    {
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function definirParametros(
        ?int $localId = null,
        ?int $periodoContableId = null,
        string $fechaOperacion = ''
    ): void {
        $this->localId = $localId ?? $this->localId;
        $this->periodoContableId = $periodoContableId ?? $this->periodoContableId;

        if ($fechaOperacion !== '') {
            $this->fechaOperacion = $fechaOperacion;
        }

        $this->showModal = false;
    }

    public function updatedOthers($value, $key): void
    {
        [$monedaId, $campo] = array_pad(
            explode('.', (string) $key, 2),
            2,
            null
        );

        if ($campo !== 'desglose' || !isset($this->others[$monedaId])) {
            return;
        }

        $importe = $this->normalizarDecimal($value);

        if ($importe === null) {
            $this->addError(
                "others.{$monedaId}.desglose",
                'Introduce un importe numérico válido.'
            );

            return;
        }

        if (bccomp($importe, '0', self::CALC_SCALE) < 0) {
            $this->addError(
                "others.{$monedaId}.desglose",
                'El importe debe ser mayor o igual a cero.'
            );

            return;
        }

        $this->others[$monedaId]['desglose'] = $importe;

        $this->resetValidation("others.{$monedaId}.desglose");

        $this->recalcularDesglosePrincipal();
    }

    public function updatedPrimary($value, $key): void
    {
        if ($key !== 'desglose' || !$this->primary) {
            return;
        }

        $importe = $this->normalizarDecimal($value);

        if ($importe === null) {
            $this->addError(
                'primary.desglose',
                'Introduce un importe numérico válido.'
            );

            return;
        }

        $this->primary['desglose'] = $importe;

        $this->resetValidation('primary.desglose');
    }

    public function updatedValorVenta($value): void
    {
        $importe = $this->normalizarDecimal($value);

        if ($importe === null || bccomp($importe, '0', self::CALC_SCALE) < 0) {
            $this->addError(
                'valorVenta',
                'El valor de la venta debe ser un número mayor o igual a cero.'
            );

            return;
        }

        $this->valorVenta = $importe;
        $this->resetValidation('valorVenta');

        $this->recalcularDesglosePrincipal();
    }

    public function loadMonedas(): void
    {
        $monedas = collect(
            app(MonedaService::class)->listForDisplay()
        )->map(fn (array $moneda) => [
            ...$moneda,
            'desglose' => '0',
        ]);

        $this->primary = $monedas->firstWhere('es_principal', true) ?? [];

        $this->others = $monedas
            ->reject(fn (array $moneda) => (bool) $moneda['es_principal'])
            ->keyBy('id')
            ->all();

        $this->originalOthers = collect($this->others)
            ->map(fn (array $moneda) => $moneda['desglose'])
            ->all();

        $this->resetValidation();

        $this->recalcularDesglosePrincipal();
    }

    private function normalizarDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $value = trim((string) $value);

        if (!preg_match('/^-?\d+(?:\.\d+)?$/D', $value)) {
            return null;
        }

        return $value;
    }

    private function decimal(mixed $value): string
    {
        return $this->normalizarDecimal($value) ?? '0';
    }

    private function tasaPrincipal(): string
    {
        return $this->decimal($this->primary['eq_per_primary'] ?? 1);
    }

    private function importeConvertido(array $moneda): string
    {
        return bcmul(
            $this->decimal($moneda['desglose'] ?? 0),
            $this->decimal($moneda['eq_per_primary'] ?? 1),
            self::CALC_SCALE
        );
    }

    private function totalOtrasMonedas(): string
    {
        $total = '0';

        foreach ($this->others as $moneda) {
            $total = bcadd(
                $total,
                $this->importeConvertido($moneda),
                self::CALC_SCALE
            );
        }

        return $total;
    }

    private function recalcularDesglosePrincipal(): void
    {
        if (!$this->primary) {
            return;
        }

        $tasaPrincipal = $this->tasaPrincipal();

        if (bccomp($tasaPrincipal, '0', self::CALC_SCALE) <= 0) {
            $this->addError(
                'primary.desglose',
                'La tasa de cambio de la moneda principal debe ser mayor que cero.'
            );

            return;
        }

        $restante = bcsub(
            $this->decimal($this->valorVenta),
            $this->totalOtrasMonedas(),
            self::CALC_SCALE
        );

        $this->primary['desglose'] = bcdiv(
            $restante,
            $tasaPrincipal,
            self::CALC_SCALE
        );

        $this->resetValidation('primary.desglose');
    }

    public function importeDesglose(?int $monedaId = null): string
    {
        if ($monedaId !== null) {
            if ((int) ($this->primary['id'] ?? 0) === $monedaId) {
                return $this->primary
                    ? $this->importeConvertido($this->primary)
                    : '0';
            }

            if (isset($this->others[$monedaId])) {
                return $this->importeConvertido($this->others[$monedaId]);
            }

            return '0';
        }

        $total = $this->primary
            ? $this->importeConvertido($this->primary)
            : '0';

        foreach ($this->others as $moneda) {
            $total = bcadd(
                $total,
                $this->importeConvertido($moneda),
                self::CALC_SCALE
            );
        }

        return $total;
    }

    public function monedaPrincipalNegativa(): bool
    {
        return $this->primary
            && bccomp(
                $this->decimal($this->primary['desglose'] ?? 0),
                '0',
                self::CALC_SCALE
            ) < 0;
    }

    public function desgloseCoincide(): bool
    {
        return bccomp(
            $this->importeDesglose(),
            $this->decimal($this->valorVenta),
            $this->decimals
        ) === 0;
    }

    public function diferenciaDesglose(): string
    {
        return bcsub(
            $this->importeDesglose(),
            $this->decimal($this->valorVenta),
            self::CALC_SCALE
        );
    }

    public function hayCambios(): bool
    {
        foreach ($this->others as $id => $moneda) {
            if (
                (string) ($moneda['desglose'] ?? '0')
                !== (string) ($this->originalOthers[$id] ?? '0')
            ) {
                return true;
            }
        }

        return false;
    }

    private function desgloseDemo(): array
    {
        return [
            [
                'id' => 1,
                'nombre' => 'Yunger Azcuy Torrens',
                'moneda' => 'CUP',
                'numero' => '9227-9598-7945-1311',
                'monto' => 12000,
                'dias_venta' => [1, 2, 3, 4, 5, 6],
            ],
            [
                'id' => 2,
                'nombre' => 'Anais Torrens Perez',
                'monto' => 20500,
                'moneda' => 'CUP',
                'numero' => '9206-9598-7327-9780',
                'dias_venta' => [1, 2, 3, 4, 5],
            ],
            [
                'id' => 3,
                'nombre' => 'Anyelaine Mena Azcuy',
                'monto' => 3000,
                'moneda' => 'CLASICA',
                'numero' => '9227-9598-7811-3351',
                'dias_venta' => [1, 3, 5, 6],
            ],
            [
                'id' => 4,
                'nombre' => 'Estefany Azcuy',
                'monto' => 850,
                'moneda' => 'USD',
                'numero' => '9205-9598-7974-0786',
                'dias_venta' => [1, 2, 3, 4, 5, 6],
            ],
        ];
    }

    private function generarDatosDemo(?PeriodoContable $periodo): array
    {
        if (
            !$periodo ||
            !$this->fechaOperacion ||
            !$this->localId ||
            $periodo->local_id != $this->localId
        ) {
            return [];
        }

        $inicio = Carbon::parse($periodo->fecha_inicio)->startOfDay();
        $fin = Carbon::parse($periodo->fecha_fin)->startOfDay();
        $fecha = Carbon::parse($this->fechaOperacion)->startOfDay();

        if ($fecha->lt($inicio) || $fecha->gt($fin)) {
            return [];
        }

        $grupo = [];

        foreach ($this->desgloseDemo() as $moneda) {
            $montoAcumulado = 0;
            $montoTotal = 0;
            $dia = $inicio->copy();

            while ($dia->lte($fecha)) {
                if (in_array($dia->dayOfWeekIso, $moneda['dias_venta'], true)) {
                    $montoAcumulado += $moneda['monto'] * 8;
                    $montoTotal += $moneda['monto'] * 10;
                }

                $dia->addDay();
            }

            $monedaHoy = in_array(
                $fecha->dayOfWeekIso,
                $moneda['dias_venta'],
                true
            );

            $grupo[] = [
                'id' => $moneda['id'],
                'nombre' => $moneda['nombre'],
                'moneda' => $moneda['moneda'],
                'numero' => $moneda['numero'] ?? '',
                'localId' => (string) $this->localId,
                'periodoContableId' => (string) $periodo->id,
                'fecha' => $fecha->format('d/m/Y'),
                'fecha_iso' => $fecha->format('Y-m-d'),
                'monto' => $monedaHoy ? $moneda['monto'] : 0,
                'monto_acumulado' => $montoAcumulado,
                'monto_total' => $montoTotal,
                'trabajo_hoy' => $monedaHoy,
            ];
        }

        return $grupo;
    }

    public function render()
    {
        $periodo = $this->periodoContableId
            ? PeriodoContable::query()
                ->whereKey($this->periodoContableId)
                ->where('local_id', $this->localId)
                ->first()
            : null;

        return view('livewire.ventas.desglose', [
            'periodoSeleccionado' => $periodo,
        ]);
    }
}
