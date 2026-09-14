<?php

namespace App\Modules\Inventario\Producto\Livewire;

use App\Models\AtributableAtributo;
use App\Models\Atributo;
use App\Models\Producto;
use Livewire\Attributes\On;
use Livewire\Component;

use App\Modules\Inventario\Producto\Actions\SaveProductoAttributesAction;
use App\Modules\Inventario\Producto\DTOs\SaveProductoAttributesDTO;

class ProductoAttributesManager extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Producto
    |--------------------------------------------------------------------------
    */

    public ?int $productoId = null;

    public ?string $productoNombre = null;

    /*
    |--------------------------------------------------------------------------
    | Cards
    |--------------------------------------------------------------------------
    |
    | Fuente de verdad del estado actual del producto.
    |
    | Estructura:
    |
    | [
    |     1 => [
    |         'atributo' => 'Color',
    |         'orden_visual' => 1,
    |         'valores' => [
    |             1 => [
    |                 'valor' => 'Rojo',
    |                 'orden_visual' => 1,
    |             ],
    |             2 => [
    |                 'valor' => 'Azul',
    |                 'orden_visual' => 2,
    |             ],
    |         ],
    |     ],
    | ]
    |
    */

    public array $cards = [];

    /*
    |--------------------------------------------------------------------------
    | Estado original
    |--------------------------------------------------------------------------
    |
    | Snapshot tomado desde BD al abrir la edición.
    |
    | No se modifica durante las operaciones en memoria.
    |
    */

    public array $originalCards = [];

    /*
    |--------------------------------------------------------------------------
    | Atributos disponibles para agregar
    |--------------------------------------------------------------------------
    |
    | Atributos que no pertenecen actualmente al producto.
    |
    */

    public array $availableAtributos = [];

    /*
    |--------------------------------------------------------------------------
    | Atributos sugeridos por la categoría padre
    |--------------------------------------------------------------------------
    |
    | Las sugerencias NO se agregan automáticamente a $cards.
    |
    | Solamente sirven como fuente adicional para seleccionar atributos.
    |
    */

    public array $suggestedAtributos = [];

    /*
    |--------------------------------------------------------------------------
    | Agregar atributo nuevo
    |--------------------------------------------------------------------------
    */

    public ?int $selectedAtributoId = null;

    public array $availableValores = [];

    public array $selectedValores = [];

    /*
    |--------------------------------------------------------------------------
    | Agregar valores a atributo existente
    |--------------------------------------------------------------------------
    */

    // Agregar valores a atributo existente

    public ?int $selectedExistingAtributoId = null;


    public array $availableExistingAtributos = [];

    public array $availableExistingValores = [];

    public array $selectedExistingValores = [];

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    public bool $show = false;

    // Atributo sugerido
    public ?int $selectedSuggestedAtributoId = null;
    public array $availableSuggestedValores = [];
    public array $selectedSuggestedValores = [];

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(?int $productoId = null): void
    {
        $this->productoId = $productoId;

        if ($this->productoId) {
            $this->cargarDatos();
        }
    }

    protected function cargarAtributosExistentes(): void
    {
        $this->availableExistingAtributos = [];

        if (empty($this->cards)) {
            return;
        }

        $this->availableExistingAtributos = Atributo::query()
            ->whereIn(
                'id',
                array_map(
                    'intval',
                    array_keys($this->cards)
                )
            )
            ->orderBy('orden_visual')
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(function ($atributo) {
                return [
                    $atributo->id => $atributo->nombre,
                ];
            })
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Abrir edición
    |--------------------------------------------------------------------------
    */

    #[On('producto-attributes-editar')]
    public function editarAtributos(int $id): void
    {
        $this->productoId = $id;

        $this->resetEstadoTemporal();

        $this->cargarDatos();

        $this->show = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar todos los datos necesarios
    |--------------------------------------------------------------------------
    */

    protected function cargarDatos(): void
    {
        $this->cargarCards();

        $this->cargarAtributosDisponibles();

        $this->cargarAtributosSugeridos();

        $this->cargarAtributosExistentes();
    }

    /*
    |--------------------------------------------------------------------------
    | Reset de controles temporales
    |--------------------------------------------------------------------------
    */

    protected function resetEstadoTemporal(): void
    {
        $this->productoNombre = null;

        $this->cards = [];

        $this->originalCards = [];

        $this->availableAtributos = [];

        $this->suggestedAtributos = [];

        $this->selectedAtributoId = null;

        $this->availableValores = [];

        $this->selectedValores = [];

        $this->selectedExistingAtributoId = null;

        $this->availableExistingValores = [];

        $this->selectedExistingValores = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar atributos actuales del producto
    |--------------------------------------------------------------------------
    |
    | Construye $cards directamente desde las relaciones del producto.
    |
    */

    protected function cargarCards(): void
    {
        if (!$this->productoId) {
            $this->cards = [];
            $this->originalCards = [];
            return;
        }


        $producto = Producto::find($this->productoId);

        if (!$producto) {
            $this->cards = [];
            $this->originalCards = [];
            return;
        }

        $this->productoNombre = $producto->nombre;

        /*
        * --------------------------------------------------------------------------
        * Obtener los atributos reales del producto.
        *
        * El orden del atributo pertenece a atributables_atributos.
        * --------------------------------------------------------------------------
        */
        $atributos = AtributableAtributo::query()
            ->with('atributo')
            ->where(
                'atributable_type',
                Producto::class
            )
            ->where(
                'atributable_id',
                $producto->id
            )
            ->orderBy('orden_visual')
            ->get();

        /*
        * --------------------------------------------------------------------------
        * Obtener los valores reales del producto.
        *
        * El orden de cada valor pertenece a atributables_valores.
        * --------------------------------------------------------------------------
        */
        $valores = $producto
            ->atributosValores()
            ->with('atributo')
            ->get()
            ->groupBy('atributo_id');

        $cards = [];

        foreach ($atributos as $atributableAtributo) {

            $atributo = $atributableAtributo->atributo;

            if (!$atributo) {
                continue;
            }

            $atributoId = (int) $atributo->id;

            /*
            * ----------------------------------------------------------------------
            * Valores asociados al atributo.
            * ----------------------------------------------------------------------
            */
            $valoresAtributo = $valores->get($atributoId, collect());

            if ($valoresAtributo->isEmpty()) {
                continue;
            }

            $valoresCard = $valoresAtributo
                ->sortBy([
                    ['pivot.orden_visual', 'asc'],
                    ['valor', 'asc'],
                ])
                ->mapWithKeys(function ($valor) {
                    return [
                        (int) $valor->id => [
                            'valor' => (string) $valor->valor,
                            'orden_visual' => (int) $valor->pivot->orden_visual,
                        ],
                    ];
                })
                ->toArray();

            if (empty($valoresCard)) {
                continue;
            }

            /*
            * ----------------------------------------------------------------------
            * Construir card.
            *
            * El orden del atributo viene de atributables_atributos.
            * NO se calcula a partir de los valores.
            * ----------------------------------------------------------------------
            */
            $cards[$atributoId] = [
                'atributo' => (string) $atributo->nombre,
                'orden_visual' => (int) $atributableAtributo->orden_visual,
                'valores' => $valoresCard,
            ];
        }

        /*
        * --------------------------------------------------------------------------
        * Ordenar cards.
        * --------------------------------------------------------------------------
        */
        uasort(
            $cards,
            function (array $a, array $b): int {
                return [
                    $a['orden_visual'],
                    $a['atributo'],
                ] <=> [
                    $b['orden_visual'],
                    $b['atributo'],
                ];
            }
        );

        /*
        * --------------------------------------------------------------------------
        * Estado actual y snapshot original.
        * --------------------------------------------------------------------------
        */
        $this->cards = $cards;
        $this->originalCards = $this->cards;

    }


    /*
    |--------------------------------------------------------------------------
    | Cargar atributos disponibles
    |--------------------------------------------------------------------------
    |
    | Son todos los atributos que todavía NO pertenecen al producto.
    |
    | Los atributos sugeridos también aparecen aquí conceptualmente,
    | pero se mantienen identificados en $suggestedAtributos.
    |
    */

    protected function cargarAtributosDisponibles(): void
    {
        $idsUsados = array_map(
            'intval',
            array_keys($this->cards)
        );

        $query = Atributo::query();

        if (!empty($idsUsados)) {
            $query->whereNotIn('id', $idsUsados);
        }

        $this->availableAtributos = $query
            ->orderBy('orden_visual')
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(function ($atributo) {
                return [
                    (int) $atributo->id => (string) $atributo->nombre,
                ];
            })
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar atributos sugeridos por la categoría padre
    |--------------------------------------------------------------------------
    |
    | Las sugerencias NO se agregan automáticamente a $cards.
    |
    | Solamente se muestran como atributos disponibles para seleccionar.
    |
    */

    protected function cargarAtributosSugeridos(): void
    {
        $this->suggestedAtributos = [];

        if (!$this->productoId) {
            return;
        }

        $producto = Producto::with('categoria')->find($this->productoId);

        if (!$producto || !$producto->categoria) {
            return;
        }

        $idsUsados = array_map(
            'intval',
            array_keys($this->cards)
        );

        $query = $producto->categoria->atributos();

        if (!empty($idsUsados)) {
            $query->whereNotIn('id', $idsUsados);
        }

        $this->suggestedAtributos = $query
            ->orderBy('orden_visual')
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(function ($atributo) {
                return [
                    (int) $atributo->id => (string) $atributo->nombre,
                ];
            })
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Seleccionar atributo para agregar
    |--------------------------------------------------------------------------
    */

    public function updatedSelectedAtributoId($value): void
    {
        $this->selectedAtributoId = $value
            ? (int) $value
            : null;

        $this->selectedValores = [];

        $this->availableValores = [];

        if (!$this->selectedAtributoId) {
            return;
        }

        $atributo = Atributo::with('valores')
            ->find($this->selectedAtributoId);

        if (!$atributo) {
            return;
        }

        $this->availableValores = $atributo->valores
            ->sortBy([
                ['orden_visual', 'asc'],
                ['valor', 'asc'],
            ])
            ->mapWithKeys(function ($valor) {
                return [
                    (int) $valor->id => [
                        'valor' => (string) $valor->valor,
                        'orden_visual' => (int) $valor->orden_visual,
                    ],
                ];
            })
            ->toArray();
    }

    /**
    |--------------------------------------------------------------------------
    | Seleccionar atributo sugerido para agregar
    |--------------------------------------------------------------------------
    */

    public function updatedSelectedSuggestedAtributoId($atributoId): void
    {
        $this->availableSuggestedValores = [];
        $this->selectedSuggestedValores = [];

        if (!$atributoId) {
            return;
        }

        $atributoId = (int) $atributoId;

        if (isset($this->cards[$atributoId])) {
            return;
        }

        $atributo = Atributo::with('valores')
            ->find($atributoId);

        if (!$atributo) {
            return;
        }

        $this->availableSuggestedValores = $atributo->valores
            ->sortBy([
                ['orden_visual', 'asc'],
                ['valor', 'asc'],
            ])
            ->mapWithKeys(function ($valor) {
                return [
                    (int) $valor->id => [
                        'valor' => (string) $valor->valor,
                        'orden_visual' => (int) $valor->orden_visual,
                    ],
                ];
            })
            ->toArray();
    }

    /**
    |--------------------------------------------------------------------------
    | Agregar atributo nuevo en memoria
    |--------------------------------------------------------------------------
    */

    public function agregarAtributo(): void
    {
        if (!$this->selectedAtributoId) {
            return;
        }

        if (empty($this->selectedValores)) {
            return;
        }

        $atributo = Atributo::with('valores')
            ->find($this->selectedAtributoId);

        if (!$atributo) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Evitar duplicados
        |--------------------------------------------------------------------------
        */

        if (isset($this->cards[(int) $atributo->id])) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Valores seleccionados
        |--------------------------------------------------------------------------
        */

        $idsSeleccionados = collect($this->selectedValores)
            ->map(fn ($id) => (int) $id)
            ->values();

        $valores = $atributo->valores
            ->whereIn('id', $idsSeleccionados)
            ->sortBy([
                ['orden_visual', 'asc'],
                ['valor', 'asc'],
            ])
            ->mapWithKeys(function ($valor) {
                return [
                    (int) $valor->id => [
                        'valor' => (string) $valor->valor,
                        'orden_visual' => (int) $valor->orden_visual,
                    ],
                ];
            })
            ->toArray();

        if (empty($valores)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Determinar orden del nuevo atributo
        |--------------------------------------------------------------------------
        */

        $ordenVisual = empty($this->cards)
            ? 1
            : (
                collect($this->cards)
                    ->max('orden_visual') + 1
            );

        /*
        |--------------------------------------------------------------------------
        | Crear card
        |--------------------------------------------------------------------------
        */

        $this->cards[(int) $atributo->id] = [
            'atributo' => (string) $atributo->nombre,
            'orden_visual' => (int) $ordenVisual,
            'valores' => $valores,
        ];

        /*
        |--------------------------------------------------------------------------
        | Reordenar cards por orden_visual
        |--------------------------------------------------------------------------
        */

        $this->ordenarCards();

        /*
        |--------------------------------------------------------------------------
        | Actualizar listas
        |--------------------------------------------------------------------------
        */

        $this->cargarAtributosDisponibles();

        $this->cargarAtributosSugeridos();

        $this->cargarAtributosExistentes();

        /*
        |--------------------------------------------------------------------------
        | Limpiar selector
        |--------------------------------------------------------------------------
        */

        $this->selectedAtributoId = null;

        $this->availableValores = [];

        $this->selectedValores = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Seleccionar atributo sugerido existente
    |--------------------------------------------------------------------------
    */
    public function agregarAtributoSugerido(): void
    {
        if (!$this->selectedSuggestedAtributoId) {
            return;
        }

        if (empty($this->selectedSuggestedValores)) {
            return;
        }

        $atributoId = (int) $this->selectedSuggestedAtributoId;

        if (isset($this->cards[$atributoId])) {
            return;
        }

        $atributo = Atributo::with('valores')
            ->find($atributoId);

        if (!$atributo) {
            return;
        }

        $ordenAtributo = empty($this->cards)
            ? 1
            : max(
                array_column($this->cards, 'orden_visual')
            ) + 1;

        $valores = [];

        $ordenValor = 1;

        foreach ($this->selectedSuggestedValores as $valorId) {

            $valorId = (int) $valorId;

            $valor = $atributo->valores
                ->firstWhere('id', $valorId);

            if (!$valor) {
                continue;
            }

            $valores[$valorId] = [
                'valor' => (string) $valor->valor,
                'orden_visual' => $ordenValor++,
            ];
        }

        if (empty($valores)) {
            return;
        }

        $this->cards[$atributoId] = [
            'atributo' => (string) $atributo->nombre,
            'orden_visual' => $ordenAtributo,
            'valores' => $valores,
        ];

        $this->ordenarCards();

        // Actualizar las tres fuentes de selección
        $this->cargarAtributosDisponibles();
        $this->cargarAtributosSugeridos();
        $this->cargarAtributosExistentes();

        // Limpiar selector de sugeridos
        $this->selectedSuggestedAtributoId = null;
        $this->availableSuggestedValores = [];
        $this->selectedSuggestedValores = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Seleccionar atributo existente
    |--------------------------------------------------------------------------
    */

    public function updatedSelectedExistingAtributoId($value): void
    {
        $this->selectedExistingAtributoId = $value
            ? (int) $value
            : null;

        $this->selectedExistingValores = [];

        $this->availableExistingValores = [];

        if (!$this->selectedExistingAtributoId) {
            return;
        }

        $this->cargarValoresParaAtributoExistente(
            $this->selectedExistingAtributoId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar valores de atributo existente
    |--------------------------------------------------------------------------
    */

    protected function cargarValoresParaAtributoExistente(
        int $atributoId
    ): void {
        /*
        |--------------------------------------------------------------------------
        | El atributo debe existir actualmente en $cards
        |--------------------------------------------------------------------------
        */

        if (!isset($this->cards[$atributoId])) {
            return;
        }

        $atributo = Atributo::with('valores')
            ->find($atributoId);

        if (!$atributo) {
            return;
        }

        $idsAsociados = array_map(
            'intval',
            array_keys($this->cards[$atributoId]['valores'] ?? [])
        );

        $this->availableExistingValores = $atributo->valores
            ->sortBy([
                ['orden_visual', 'asc'],
                ['valor', 'asc'],
            ])
            ->mapWithKeys(function ($valor) use ($idsAsociados) {
                return [
                    (int) $valor->id => [
                        'valor' => (string) $valor->valor,
                        'orden_visual' => (int) $valor->orden_visual,
                        'asociado' => in_array(
                            (int) $valor->id,
                            $idsAsociados,
                            true
                        ),
                    ],
                ];
            })
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Agregar valores a atributo existente
    |--------------------------------------------------------------------------
    */

    public function agregarValores(): void
    {
        if (!$this->selectedExistingAtributoId) {
            return;
        }

        if (empty($this->selectedExistingValores)) {
            return;
        }

        $atributoId = (int) $this->selectedExistingAtributoId;

        if (!isset($this->cards[$atributoId])) {
            return;
        }

        $atributo = Atributo::with('valores')
            ->find($atributoId);

        if (!$atributo) {
            return;
        }

        $idsActuales = array_map(
            'intval',
            array_keys($this->cards[$atributoId]['valores'] ?? [])
        );

        foreach ($this->selectedExistingValores as $valorId) {
            $valorId = (int) $valorId;

            if (in_array($valorId, $idsActuales, true)) {
                continue;
            }

            $valor = $atributo->valores
                ->firstWhere('id', $valorId);

            if (!$valor) {
                continue;
            }

            $this->cards[$atributoId]['valores'][$valorId] = [
                'valor' => (string) $valor->valor,
                'orden_visual' => (int) $valor->orden_visual,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Ordenar valores
        |--------------------------------------------------------------------------
        */

        $valores = $this->cards[$atributoId]['valores'];

        uasort(
            $valores,
            function (array $a, array $b): int {
                return [
                    $a['orden_visual'],
                    $a['valor'],
                ] <=> [
                    $b['orden_visual'],
                    $b['valor'],
                ];
            }
        );

        $this->cards[$atributoId]['valores'] = $valores;

        /*
        |--------------------------------------------------------------------------
        | Actualizar selector
        |--------------------------------------------------------------------------
        */

        //$this->cargarValoresParaAtributoExistente($atributoId);

        $this->selectedExistingValores = [];

        $this->selectedExistingAtributoId = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar atributo en memoria
    |--------------------------------------------------------------------------
    */

    public function eliminarAtributo(int $atributoId): void
    {
        $atributoId = (int) $atributoId;

        if (!isset($this->cards[$atributoId])) {
            return;
        }

        unset($this->cards[$atributoId]);

        /*
        |--------------------------------------------------------------------------
        | Reindexar manteniendo ID como clave
        |--------------------------------------------------------------------------
        */

        $this->ordenarCards();

        /*
        |--------------------------------------------------------------------------
        | Si era el atributo seleccionado para agregar valores
        |--------------------------------------------------------------------------
        */

        if (
            $this->selectedExistingAtributoId &&
            (int) $this->selectedExistingAtributoId === $atributoId
        ) {
            $this->selectedExistingAtributoId = null;

            $this->availableExistingValores = [];

            $this->selectedExistingValores = [];
        }

        /*
        |--------------------------------------------------------------------------
        | Actualizar disponibles y sugeridos
        |--------------------------------------------------------------------------
        */

        $this->cargarAtributosDisponibles();

        $this->cargarAtributosSugeridos();

        $this->cargarAtributosExistentes();
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar valor en memoria
    |--------------------------------------------------------------------------
    */

    public function eliminarValor(
        int $atributoId,
        int $valorId
    ): void {
        $atributoId = (int) $atributoId;
        $valorId = (int) $valorId;

        if (!isset($this->cards[$atributoId])) {
            return;
        }

        if (!isset($this->cards[$atributoId]['valores'][$valorId])) {
            return;
        }

        unset(
            $this->cards[$atributoId]['valores'][$valorId]
        );

        /*
        |--------------------------------------------------------------------------
        | Si el atributo quedó sin valores,
        | desaparece también del estado.
        |--------------------------------------------------------------------------
        */

        if (empty($this->cards[$atributoId]['valores'])) {
            unset($this->cards[$atributoId]);
        }

        /*
        |--------------------------------------------------------------------------
        | Ordenar nuevamente
        |--------------------------------------------------------------------------
        */

        //$this->ordenarCards();

        /*
        |--------------------------------------------------------------------------
        | Limpiar selector si corresponde
        |--------------------------------------------------------------------------
        */

        if (
            $this->selectedExistingAtributoId &&
            (int) $this->selectedExistingAtributoId === $atributoId
        ) {
            $this->selectedExistingAtributoId = null;

            $this->availableExistingValores = [];

            $this->selectedExistingValores = [];
        }

        /*
        |--------------------------------------------------------------------------
        | Actualizar disponibles y sugeridos
        |--------------------------------------------------------------------------
        */

        $this->cargarAtributosDisponibles();

        $this->cargarAtributosSugeridos();

        $this->cargarAtributosExistentes();
    }

    public function moverAtributoArriba(int $atributoId): void
    {
        $atributoId = (int) $atributoId;

        $ids = array_keys($this->cards);

        $posicionActual = array_search($atributoId, $ids, true);

        if ($posicionActual === false || $posicionActual === 0) {
            return;
        }

        $destinoId = $ids[$posicionActual - 1];

        $this->reordenarAtributos(
            $atributoId,
            $destinoId
        );
    }

    public function moverAtributoAbajo(int $atributoId): void
    {
        $atributoId = (int) $atributoId;

        $ids = array_keys($this->cards);

        $posicionActual = array_search($atributoId, $ids, true);

        if (
            $posicionActual === false ||
            $posicionActual === count($ids) - 1
        ) {
            return;
        }

        $destinoId = $ids[$posicionActual + 1];

        $this->reordenarAtributos(
            $atributoId,
            $destinoId
        );
    }


    protected function actualizarOrdenAtributos(): void
    {
        $orden = 1;

        foreach ($this->cards as &$card) {
            $card['orden_visual'] = $orden++;
        }

        unset($card);
    }

    /**
     * |--------------------------------------------------------------------------
     * | Mover valor arriba
     * |--------------------------------------------------------------------------
     */
    public function moverValorArriba(
        int $atributoId,
        int $valorId
    ): void {
        $atributoId = (int) $atributoId;
        $valorId = (int) $valorId;

        if (!isset($this->cards[$atributoId])) {
            return;
        }

        $valores = $this->cards[$atributoId]['valores'] ?? [];

        if (!isset($valores[$valorId])) {
            return;
        }

        $ids = array_keys($valores);

        $index = array_search($valorId, $ids, true);

        if ($index === false || $index === 0) {
            return;
        }

        /*
        * Intercambiar con el valor anterior.
        */
        $anterior = $ids[$index - 1];

        [$ids[$index - 1], $ids[$index]] = [
            $ids[$index],
            $ids[$index - 1],
        ];

        /*
        * Reconstruir valores manteniendo el ID como clave.
        */
        $valoresOrdenados = [];

        foreach ($ids as $orden => $id) {
            $valoresOrdenados[$id] = $valores[$id];
            $valoresOrdenados[$id]['orden_visual'] = $orden + 1;
        }

        $this->cards[$atributoId]['valores'] = $valoresOrdenados;
    }

    /**
     * |--------------------------------------------------------------------------
     * | Mover valor abajo
     * |--------------------------------------------------------------------------
     */
    public function moverValorAbajo(
        int $atributoId,
        int $valorId
    ): void {
        $atributoId = (int) $atributoId;
        $valorId = (int) $valorId;

        if (!isset($this->cards[$atributoId])) {
            return;
        }

        $valores = $this->cards[$atributoId]['valores'] ?? [];

        if (!isset($valores[$valorId])) {
            return;
        }

        $ids = array_keys($valores);

        $index = array_search($valorId, $ids, true);

        if ($index === false || $index === count($ids) - 1) {
            return;
        }

        /*
        * Intercambiar con el valor siguiente.
        */
        [$ids[$index], $ids[$index + 1]] = [
            $ids[$index + 1],
            $ids[$index],
        ];

        /*
        * Reconstruir valores manteniendo el ID como clave.
        */
        $valoresOrdenados = [];

        foreach ($ids as $orden => $id) {
            $valoresOrdenados[$id] = $valores[$id];
            $valoresOrdenados[$id]['orden_visual'] = $orden + 1;
        }

        $this->cards[$atributoId]['valores'] = $valoresOrdenados;
    }

    /*
    |--------------------------------------------------------------------------
    | Reordenar atributos en memoria
    |--------------------------------------------------------------------------
    */

    public function reordenarAtributos(
        int $origenId,
        int $destinoId
    ): void {
        $origenId = (int) $origenId;
        $destinoId = (int) $destinoId;

        if (
            !isset($this->cards[$origenId]) ||
            !isset($this->cards[$destinoId])
        ) {
            return;
        }

        if ($origenId === $destinoId) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener orden actual
        |--------------------------------------------------------------------------
        */

        $ids = array_keys($this->cards);

        $origenIndex = array_search(
            $origenId,
            $ids,
            true
        );

        $destinoIndex = array_search(
            $destinoId,
            $ids,
            true
        );

        if (
            $origenIndex === false ||
            $destinoIndex === false ||
            $origenIndex === $destinoIndex
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Mover ID
        |--------------------------------------------------------------------------
        */

        $movido = array_splice(
            $ids,
            $origenIndex,
            1
        );

        array_splice(
            $ids,
            $destinoIndex,
            0,
            $movido
        );

        /*
        |--------------------------------------------------------------------------
        | Reconstruir cards respetando el nuevo orden
        |--------------------------------------------------------------------------
        */

        $cardsOrdenadas = [];

        foreach ($ids as $index => $id) {
            $cardsOrdenadas[$id] = $this->cards[$id];

            /*
            |--------------------------------------------------------------------------
            | El orden visual pertenece a la categoría/producto,
            | no al catálogo maestro.
            |--------------------------------------------------------------------------
            */

            $cardsOrdenadas[$id]['orden_visual'] = $index + 1;
        }

        $this->cards = $cardsOrdenadas;
    }

    /*
    |--------------------------------------------------------------------------
    | Reordenar valores en memoria
    |--------------------------------------------------------------------------
    */

    public function reordenarValores(
        int $atributoId,
        int $origenValorId,
        int $destinoValorId
    ): void {
        $atributoId = (int) $atributoId;
        $origenValorId = (int) $origenValorId;
        $destinoValorId = (int) $destinoValorId;

        if (!isset($this->cards[$atributoId])) {
            return;
        }

        $valores = $this->cards[$atributoId]['valores'] ?? [];

        if (
            !isset($valores[$origenValorId]) ||
            !isset($valores[$destinoValorId])
        ) {
            return;
        }

        if ($origenValorId === $destinoValorId) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Obtener orden actual
        |--------------------------------------------------------------------------
        */

        $ids = array_keys($valores);

        $origenIndex = array_search(
            $origenValorId,
            $ids,
            true
        );

        $destinoIndex = array_search(
            $destinoValorId,
            $ids,
            true
        );

        if (
            $origenIndex === false ||
            $destinoIndex === false ||
            $origenIndex === $destinoIndex
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Mover valor
        |--------------------------------------------------------------------------
        */

        $movido = array_splice(
            $ids,
            $origenIndex,
            1
        );

        array_splice(
            $ids,
            $destinoIndex,
            0,
            $movido
        );

        /*
        |--------------------------------------------------------------------------
        | Reconstruir valores respetando nuevo orden
        |--------------------------------------------------------------------------
        */

        $valoresOrdenados = [];

        foreach ($ids as $index => $id) {
            $valoresOrdenados[$id] = $valores[$id];

            $valoresOrdenados[$id]['orden_visual'] = $index + 1;
        }

        $this->cards[$atributoId]['valores'] = $valoresOrdenados;
    }

    /*
    |--------------------------------------------------------------------------
    | Ordenar cards
    |--------------------------------------------------------------------------
    */

    protected function ordenarCards(): void
    {
        uasort(
            $this->cards,
            function (array $a, array $b): int {
                return [
                    $a['orden_visual'],
                    $a['atributo'],
                ] <=> [
                    $b['orden_visual'],
                    $b['atributo'],
                ];
            }
        );

        // $orden = 1;

        // foreach ($this->cards as &$card) {
        //     $card['orden_visual'] = $orden++;
        // }

        // unset($card);
    }

    /*
    |--------------------------------------------------------------------------
    | Normalizar cards para comparación
    |--------------------------------------------------------------------------
    */

    protected function normalizarCards(array $cards): array
    {
        $resultado = [];

        foreach ($cards as $atributoId => $card) {
            $valores = [];

            foreach ($card['valores'] ?? [] as $valorId => $valor) {
                $valores[(int) $valorId] = [
                    'valor' => (string) $valor['valor'],
                    'orden_visual' => (int) $valor['orden_visual'],
                ];
            }

            $resultado[(int) $atributoId] = [
                'atributo' => (string) $card['atributo'],
                'orden_visual' => (int) $card['orden_visual'],
                'valores' => $valores,
            ];
        }

        return $resultado;
    }

    /*
    |--------------------------------------------------------------------------
    | Determinar si existen cambios
    |--------------------------------------------------------------------------
    */

    public function hayCambios(): bool
    {
        return $this->normalizarCards($this->cards)
            !== $this->normalizarCards($this->originalCards);
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    |
    | Por ahora solamente prueba/diagnóstico.
    | La persistencia se implementará posteriormente.
    |
    */

    public function guardar(): void
    {
        if (!$this->productoId) {
            logger()->warning(
                'PRODUCTO ATTRIBUTES - GUARDAR SIN PRODUCTO',
                [
                'producto_id' => $this->productoId,
                ]
            );

            return;
        }

        $producto = Producto::find($this->productoId);

        if (!$producto) {
            logger()->warning(
                'PRODUCTO ATTRIBUTES - GUARDAR PRODUCTO NO EXISTE',
                [
                    'producto_id' => $this->productoId,
                ]
            );

            return;
        }

        if (!$this->hayCambios()) {
            logger()->info(
                'PRODUCTO ATTRIBUTES - GUARDAR SIN CAMBIOS',
                [
                    'producto_id' => $this->productoId,
                ]
            );

            return;
        }

        logger()->info(
            'PRODUCTO ATTRIBUTES - GUARDAR INICIO',
            [
                'producto_id' => $this->productoId,
                'original' => $this->originalCards,
                'actual' => $this->cards,
            ]
        );

        $dto = new SaveProductoAttributesDTO(
            productoId: (int) $this->productoId,
            cards: $this->cards,
        );

        app(SaveProductoAttributesAction::class)
            ->execute($dto);

        /*
        * --------------------------------------------------------------------------
        * Recargar desde BD
        * --------------------------------------------------------------------------
        *
        * No dejamos simplemente:
        *
        * $this->originalCards = $this->cards;
        *
        * Volvemos a consultar la BD para que el estado de Livewire represente
        * exactamente lo que quedó persistido.
        */
        $this->cargarCards();

        logger()->info(
            'PRODUCTO ATTRIBUTES - GUARDAR OK',
            [
                'producto_id' => $this->productoId,
                'cards' => $this->cards,
                'original' => $this->originalCards,
            ]
        );

        $this->dispatch('livewire:alert', [
            'message' => 'Atributos guardados correctamente',
            'type' => 'success',
        ]);
    }



    /*
    |--------------------------------------------------------------------------
    | Diagnóstico temporal
    |--------------------------------------------------------------------------
    */

    public function diagnosticarCambios(): void
    {
        logger()->info(
            'PRODUCTO ATTRIBUTES - DIAGNOSTICO CAMBIOS',
            [
                'producto_id' => $this->productoId,
                'hay_cambios' => $this->hayCambios(),
                'originalCards' => $this->originalCards,
                'cards' => $this->cards,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cerrar
    |--------------------------------------------------------------------------
    */

    public function cerrar(): void
    {
        $this->show = false;

        $this->resetEstadoTemporal();

        $this->productoId = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'modules.inventario.producto.attributes-manager'
        );
    }
}