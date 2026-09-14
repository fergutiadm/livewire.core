<?php

namespace App\View\Components\Inventory;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

/**
 * Componente: Totales de inversión
 *
 * Acepta items como Collection|LengthAwarePaginator|array (cada fila puede ser array u objeto stdClass).
 * Calcula sumas en moneda primaria y, si existe, en USD.
 * Modo simple: una tarjeta por Costo/Precio/Ganancia (por defecto sólo items con stock si onlyWithStock=true).
 * Modo complex: por cada tarjeta muestra En stock / En cero / Todos.
 */
class TotalesInversion extends Component
{
    /** @var Collection<int, mixed> */
    public Collection $items;

    /** @var Collection<int, mixed> */
    public Collection $currencies;

    /** Título opcional mostrado arriba del bloque. */
    public ?string $title;

    /** Si true, imprime números “crudos” (sin miles/decimales). */
    public bool $raw;

    /** Si true (por defecto), en modo simple sólo considera items con stock > 0. */
    public bool $onlyWithStock;

    /** 'simple' | 'complex' */
    public string $renderType;

    /** Código de la moneda primaria (p.ej. CUP). */
    public ?string $primaryCode = null;

    /** Tasa USD->primaria (rate_to_primary). Si no hay USD será null. */
    public ?float $usdRate = null;

    /**
     * Datos que se envían al blade:
     * - simple: [
     *     ['label'=>'Costo', 'usd'=>float|null, 'primary'=>float],
     *     ...
     *   ]
     * - complex: [
     *     ['label'=>'Costo', 'usd'=>['stock'=>..,'zero'=>..,'all'=>..]|null,
     *                        'primary'=>['stock'=>..,'zero'=>..,'all'=>..]],
     *     ...
     *   ]
     *
     * @var array<int, array<string, mixed>>
     */
    public array $cards = [];

    /**
     * @param  mixed $items        Collection|LengthAwarePaginator|array
     * @param  mixed $currencies   Collection|array
     * @param  string|null $title
     * @param  bool $raw
     * @param  bool $onlyWithStock
     * @param  string $renderType  'simple'|'complex'
     */
    public function __construct(
        mixed $items,
        mixed $currencies = null,
        ?string $title = null,
        bool $raw = false,
        bool $onlyWithStock = true,
        string $renderType = 'simple'
    ) {
        // Normaliza colecciones
        $this->items      = $this->normalizeItems($items);
        $this->currencies = $this->normalizeCurrencies($currencies);

        // Props simples
        $this->title         = $title;
        $this->raw           = $raw;
        $this->onlyWithStock = $onlyWithStock;
        $this->renderType    = in_array($renderType, ['simple', 'complex'], true) ? $renderType : 'simple';

        // Detecta primaria y USD
        $this->primaryCode = (string) data_get(
            $this->currencies->firstWhere('is_primary', true),
            'code',
            'PRIM'
        );

        $this->usdRate = null;
        $usd = $this->currencies->firstWhere('code', 'USD');
        if ($usd) {
            $r = (float) data_get($usd, 'rate_to_primary', 0);
            if ($r > 0) $this->usdRate = $r;
        }

        // Construye tarjetas según modo
        $this->cards = $this->renderType === 'complex'
            ? $this->buildCardsComplex()
            : $this->buildCardsSimple();
    }

    /* ========================= Helpers de normalización ========================= */

    private function normalizeItems(mixed $items): Collection
    {
        if ($items instanceof LengthAwarePaginator) {
            // Nos quedamos con “data” del paginador
            return collect($items->items())->map(fn ($row) => $this->toArrayObject($row));
        }
        if ($items instanceof Collection) {
            return $items->map(fn ($row) => $this->toArrayObject($row));
        }
        if (is_array($items)) {
            // Si es paginado en array (current_page, data, etc)
            if (Arr::has($items, 'data') && is_array($items['data'])) {
                return collect($items['data'])->map(fn ($row) => $this->toArrayObject($row));
            }
            return collect($items)->map(fn ($row) => $this->toArrayObject($row));
        }
        return collect();
    }

    private function toArrayObject(mixed $row): array|object
    {
        // Deja como array o stdClass indistintamente; data_get trabajará igual.
        return is_array($row) ? $row : (is_object($row) ? $row : (array) $row);
    }

    private function normalizeCurrencies(mixed $currencies): Collection
    {
        if ($currencies instanceof Collection) return $currencies->values();
        if (is_array($currencies)) return collect($currencies)->values();
        return collect(); // vacío si no viene
    }

    /* ========================= Cálculos ========================= */

    /**
     * Devuelve items filtrados por stock:
     *  - 'stock' => >0
     *  - 'zero'  => ==0
     *  - 'all'   => todos
     */
    private function filterByStock(string $kind): Collection
    {
        return match ($kind) {
            'stock' => $this->items->filter(fn ($r) => (int) data_get($r, 'stock_qty', 0) > 0)->values(),
            'zero'  => $this->items->filter(fn ($r) => (int) data_get($r, 'stock_qty', 0) === 0)->values(),
            default => $this->items->values(),
        };
    }

    /**
     * Acumula totales a moneda primaria y USD (si existe) para Costo, Precio y Ganancia
     * sobre una colección de items.
     *
     * @return array{
     *   cost:   array{primary: float, usd: float|null},
     *   price:  array{primary: float, usd: float|null},
     *   gain:   array{primary: float, usd: float|null},
     * }
     */
    private function accumulate(Collection $rows): array
    {
        $sumCostPrim = 0.0; $sumPricePrim = 0.0; $sumGainPrim = 0.0;

        foreach ($rows as $row) {
            $cost      = (float) data_get($row, 'cost', 0);
            $price     = (float) data_get($row, 'price', 0);
            $costRate  = (float) data_get($row, 'cost_rate', 0);   // rate_to_primary
            $priceRate = (float) data_get($row, 'price_rate', 0);  // rate_to_primary

            $cPrim = $costRate  > 0 ? $cost  * $costRate  : 0.0;
            $pPrim = $priceRate > 0 ? $price * $priceRate : 0.0;
            $gPrim = $pPrim - $cPrim;

            $sumCostPrim  += $cPrim;
            $sumPricePrim += $pPrim;
            $sumGainPrim  += $gPrim;
        }

        $usd = ($this->usdRate && $this->usdRate > 0) ? $this->usdRate : null;

        return [
            'cost'  => [
                'primary' => $sumCostPrim,
                'usd'     => $usd ? ($sumCostPrim  / $usd) : null,
            ],
            'price' => [
                'primary' => $sumPricePrim,
                'usd'     => $usd ? ($sumPricePrim / $usd) : null,
            ],
            'gain'  => [
                'primary' => $sumGainPrim,
                'usd'     => $usd ? ($sumGainPrim  / $usd) : null,
            ],
        ];
    }

    /** Construye tarjetas para modo SIMPLE. */
    private function buildCardsSimple(): array
    {
        $rows = $this->onlyWithStock ? $this->filterByStock('stock') : $this->filterByStock('all');
        $acc  = $this->accumulate($rows);
        $usdExists = !is_null($acc['cost']['usd']) || !is_null($acc['price']['usd']) || !is_null($acc['gain']['usd']);

        return [
            [
                'label'   => 'Costo',
                'usd'     => $usdExists ? $acc['cost']['usd']  : null,
                'primary' => $acc['cost']['primary'],
            ],
            [
                'label'   => 'Precio',
                'usd'     => $usdExists ? $acc['price']['usd'] : null,
                'primary' => $acc['price']['primary'],
            ],
            [
                'label'   => 'Ganancia',
                'usd'     => $usdExists ? $acc['gain']['usd']  : null,
                'primary' => $acc['gain']['primary'],
            ],
        ];
    }

    /** Construye tarjetas para modo COMPLEX (en stock / en cero / todos). */
    private function buildCardsComplex(): array
    {
        $accStock = $this->accumulate($this->filterByStock('stock'));
        $accZero  = $this->accumulate($this->filterByStock('zero'));
        $accAll   = $this->accumulate($this->filterByStock('all'));

        $usdExists = !is_null($accAll['cost']['usd']) || !is_null($accAll['price']['usd']) || !is_null($accAll['gain']['usd']);

        $card = function (string $label, string $key) use ($accStock, $accZero, $accAll, $usdExists) {
            return [
                'label'   => $label,
                'usd'     => $usdExists ? [
                    'stock' => $accStock[$key]['usd'],
                    'zero'  => $accZero[$key]['usd'],
                    'all'   => $accAll[$key]['usd'],
                ] : null,
                'primary' => [
                    'stock' => $accStock[$key]['primary'],
                    'zero'  => $accZero[$key]['primary'],
                    'all'   => $accAll[$key]['primary'],
                ],
            ];
        };

        return [
            $card('Costo',   'cost'),
            $card('Precio',  'price'),
            $card('Ganancia','gain'),
        ];
    }

    /* ========================= Render ========================= */

    public function render(): View|Closure|string
    {
        $view = $this->renderType === 'complex'
            ? 'components.inventory.totales-inversion-complex'
            : 'components.inventory.totales-inversion-simple';

        // Pasamos explícitamente lo que usa el blade
        return view($view, [
            'cards'       => $this->cards,
            'primaryCode' => $this->primaryCode,
            'raw'         => $this->raw,
            'title'       => $this->title,
        ]);
    }
}


// \Log::info('TotalesInversion component CLASS is rendering'); // (opcional para verificar)