<?php

namespace App\Modules\Inventario\Categoria\Livewire;

use App\Models\AtributableAtributo;
use App\Models\AtributableValor;
use App\Models\Atributo;
use App\Models\Categoria;
use App\Modules\Inventario\Categoria\Actions\SaveCategoriaAttributesAction;
use App\Modules\Inventario\Categoria\DTOs\SaveCategoriaAttributesDTO;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoriaAttributesManager extends Component
{
    public ?int $categoriaId = null;

    public ?string $categoriaNombre = null;

    public bool $show = false;

    /**
     * Matriz de trabajo actual.
     *
     * [
     *     atributoId => [
     *         'atributo' => 'Color',
     *         'valores' => [
     *             valorId => 'Rojo',
     *             valorId => 'Azul',
     *         ],
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
    | Selector de atributos nuevos
    |--------------------------------------------------------------------------
    */

    public ?int $selectedAtributoId = null;

    public array $availableAtributos = [];

    public array $availableValores = [];

    public array $selectedValores = [];

    /*
    |--------------------------------------------------------------------------
    | Selector de atributos existentes
    |--------------------------------------------------------------------------
    */

    public ?int $selectedExistingAtributoId = null;

    public array $availableExistingAtributos = [];

    public array $availableExistingValores = [];

    public array $selectedExistingValores = [];

    /*
    |--------------------------------------------------------------------------
    | Inicialización
    |--------------------------------------------------------------------------
    */

    public function mount(?int $categoriaId = null): void
    {
        $this->categoriaId = $categoriaId;

        // Log::info('CATEGORIA ATTRIBUTES MOUNT(1)', [
        //     'categoriaId' => $this->categoriaId,
        // ]);

        if ($this->categoriaId) {
            Log::info('CATEGORIA ATTRIBUTES MOUNT(2)', [
                'categoriaId' => $this->categoriaId,
            ]);

            $this->loadAttributes();
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
    * Normalizar matriz de atributos
    * ---
    *
    * La comparación se realiza sobre el estado funcional de la matriz:
    *
    * * atributo
    * * orden_visual del atributo
    * * valores
    * * orden_visual de cada valor
    *
    * El objetivo es evitar diferencias producidas únicamente por tipos
    * o estructura de índices que no representen un cambio real.
    */
    protected function normalizarCards(array $cards): array
    {
        $normalizados = [];

        foreach ($cards as $atributoId => $card) {


        $valores = [];

        foreach ($card['valores'] ?? [] as $valorId => $valor) {
            $valores[(int) $valorId] = [
                'valor' => (string) ($valor['valor'] ?? ''),
                'orden_visual' => (int) ($valor['orden_visual'] ?? 0),
            ];
        }

        $normalizados[(int) $atributoId] = [
            'atributo' => (string) ($card['atributo'] ?? ''),
            'orden_visual' => (int) ($card['orden_visual'] ?? 0),
            'valores' => $valores,
        ];

        }

    return $normalizados;
  }


    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

    public function guardar(): void
    {
        if (!$this->categoriaId) {
            Log::warning('CATEGORIA ATTRIBUTES GUARDAR SIN CATEGORIA', [
            'categoriaId' => $this->categoriaId,
            ]);

            return;
        }

        $categoria = Categoria::find($this->categoriaId);

        if (!$categoria) {
            Log::warning('CATEGORIA ATTRIBUTES GUARDAR CATEGORIA NO EXISTE', [
                'categoriaId' => $this->categoriaId,
            ]);

            return;
        }

        if (!$this->hayCambios()) {
            Log::info('CATEGORIA ATTRIBUTES GUARDAR SIN CAMBIOS', [
                'categoriaId' => $this->categoriaId,
            ]);

            return;
        }

        Log::info('CATEGORIA ATTRIBUTES GUARDAR INICIO', [
            'categoriaId' => $this->categoriaId,
            'originalCards' => $this->originalCards,
            'cards' => $this->cards,
        ]);

        $dto = new SaveCategoriaAttributesDTO(
            categoriaId: $this->categoriaId,
            cards: $this->cards,
        );

        app(SaveCategoriaAttributesAction::class)->execute($dto);

        /*
        * --------------------------------------------------------------------------
        * Recargar desde BD
        * --------------------------------------------------------------------------
        *
        * La recarga garantiza que el estado de Livewire represente exactamente
        * lo que quedó persistido.
        */
        $this->loadAttributes();

        /*
        * --------------------------------------------------------------------------
        * Mantener modal abierto y limpiar selección
        * --------------------------------------------------------------------------
        */
        $this->selectedAtributoId = null;
        $this->availableValores = [];
        $this->selectedValores = [];
        $this->selectedExistingAtributoId = null;
        $this->availableExistingValores = [];
        $this->selectedExistingValores = [];

        Log::info('CATEGORIA ATTRIBUTES GUARDADOS', [
            'categoriaId' => $this->categoriaId,
            'cards' => $this->cards,
            'originalCards' => $this->originalCards,
        ]);

        $this->dispatch('livewire:alert', [
            'message' => 'Atributos guardados correctamente',
            'type' => 'success',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Agregar atributo nuevo
    |--------------------------------------------------------------------------
    */

    public function agregarAtributo(): void
    {
        if (
            !$this->selectedAtributoId ||
            empty($this->selectedValores)
        ) {
            return;
        }

        $atributo = Atributo::with('valores')
            ->find($this->selectedAtributoId);

        if (!$atributo) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Evitar duplicar atributo
        |--------------------------------------------------------------------------
        */

        if (isset($this->cards[$atributo->id])) {
            return;
        }

        $ordenAtributo = $this->siguienteOrdenAtributo();

        $ordenValor = 1;

        $valores = [];

        foreach ($this->selectedValores as $valorId) {
            $valorId = (int) $valorId;

            $valor = $atributo->valores
                ->firstWhere('id', $valorId);

            if (!$valor) {
                continue;
            }

            $valores[$valor->id] = [
                'valor' => $valor->valor,
                'orden_visual' => $ordenValor++,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | No agregar atributo sin valores válidos
        |--------------------------------------------------------------------------
        */

        if (empty($valores)) {
            return;
        }

        $this->cards[$atributo->id] = [
            'atributo' => $atributo->nombre,
            'orden_visual' => $ordenAtributo,
            'valores' => $valores,
        ];

        /*
        |--------------------------------------------------------------------------
        | Actualizar selectores
        |--------------------------------------------------------------------------
        */

        $this->loadAvailableAtributos();
        $this->cargarAtributosExistentes();

        /*
        |--------------------------------------------------------------------------
        | Limpiar selección
        |--------------------------------------------------------------------------
        */

        $this->selectedAtributoId = null;
        $this->availableValores = [];
        $this->selectedValores = [];

        Log::info('CATEGORIA ATTRIBUTE AGREGADO EN MEMORIA', [
            'categoriaId' => $this->categoriaId,
            'atributoId' => $atributo->id,
            'atributo' => $atributo->nombre,
            'valores' => $valores,
            'cards' => $this->cards,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Agregar valores a atributo existente
    |--------------------------------------------------------------------------
    */

    public function agregarValores(): void
    {
        if (
            !$this->selectedExistingAtributoId ||
            empty($this->selectedExistingValores)
        ) {
            return;
        }

        $atributoId = (int) $this->selectedExistingAtributoId;

        /*
        * --------------------------------------------------------------------------
        * El atributo debe existir actualmente en la matriz.
        * --------------------------------------------------------------------------
        */
        if (!isset($this->cards[$atributoId])) {
            return;
        }

        $atributo = Atributo::with('valores')
            ->find($atributoId);

        if (!$atributo) {
            return;
        }

        /*
        * --------------------------------------------------------------------------
        * Obtener el siguiente orden disponible.
        * El orden visual es base 1.
        * --------------------------------------------------------------------------
        */
        $ordenValor = $this->siguienteOrdenValor($atributoId);

        foreach ($this->selectedExistingValores as $valorId) {
            $valorId = (int) $valorId;

            /*
            * ----------------------------------------------------------------------
            * Evitar duplicar un valor ya asociado.
            * ----------------------------------------------------------------------
            */
            if (isset($this->cards[$atributoId]['valores'][$valorId])) {
                continue;
            }

            $valor = $atributo->valores
                ->firstWhere('id', $valorId);

            if (!$valor) {
                continue;
            }

            $this->cards[$atributoId]['valores'][$valorId] = [
                'valor' => (string) $valor->valor,
                'orden_visual' => $ordenValor++,
            ];
        }

        Log::info('PRODUCTO ATTRIBUTE VALORES AGREGADOS EN MEMORIA', [
            'productoId' => $this->productoId,
            'atributoId' => $atributoId,
            'atributo' => $atributo->nombre,
            'valoresSeleccionados' => $this->selectedExistingValores,
            'cards' => $this->cards,
        ]);

        /*
        * --------------------------------------------------------------------------
        * Limpiar selección y valores disponibles.
        * --------------------------------------------------------------------------
        */
        $this->selectedExistingValores = [];
        $this->selectedExistingAtributoId = null;
        $this->availableExistingValores = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar valores disponibles de un atributo
    |--------------------------------------------------------------------------
    */

    protected function actualizarAvailableValores(int $atributoId): void
    {
        $atributo = Atributo::with('valores')
            ->find($atributoId);

        if (!$atributo) {
            $this->availableValores = [];

            return;
        }

        $valoresAsociados = array_keys(
            $this->cards[$atributoId]['valores'] ?? []
        );

        $valoresAsociados = array_map(
            'intval',
            $valoresAsociados
        );

        $this->availableValores = $atributo->valores
            ->sortBy([
                ['orden_visual', 'asc'],
                ['valor', 'asc'],
            ])
            ->mapWithKeys(function ($valor) use ($valoresAsociados) {
                return [
                    $valor->id => [
                        'valor' => $valor->valor,
                        'asociado' => in_array(
                            (int) $valor->id,
                            $valoresAsociados,
                            true
                        ),
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * --------------------------------------------------------------------------
     * Reordenar atributos mediante drag & drop
     * --------------------------------------------------------------------------
     */
    public function reordenarAtributos(
        int $atributoIdOrigen,
        int $atributoIdDestino
    ): void {
        if ($atributoIdOrigen === $atributoIdDestino) {
            return;
        }

        if (
            !isset($this->cards[$atributoIdOrigen]) ||
            !isset($this->cards[$atributoIdDestino])
        ) {
            return;
        }

        $cards = $this->cards;

        $atributoMovido = $cards[$atributoIdOrigen];

        unset($cards[$atributoIdOrigen]);

        $nuevoOrden = [];

        foreach ($cards as $atributoId => $card) {
            if ($atributoId === $atributoIdDestino) {
                $nuevoOrden[$atributoIdOrigen] = $atributoMovido;
            }

            $nuevoOrden[$atributoId] = $card;
        }

        $orden = 1;

        foreach ($nuevoOrden as &$card) {
            $card['orden_visual'] = $orden++;
        }

        unset($card);

        $this->cards = $nuevoOrden;

        Log::info('CATEGORIA ATTRIBUTES REORDENADOS EN MEMORIA', [
            'categoriaId' => $this->categoriaId,
            'atributoIdOrigen' => $atributoIdOrigen,
            'atributoIdDestino' => $atributoIdDestino,
            'cards' => $this->cards,
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Reordenar valores mediante drag & drop
     * --------------------------------------------------------------------------
     */
    public function reordenarValores(
        int $atributoId,
        int $valorIdOrigen,
        int $valorIdDestino
    ): void {
        if ($valorIdOrigen === $valorIdDestino) {
            return;
        }

        if (!isset($this->cards[$atributoId])) {
            return;
        }

        if (
            !isset($this->cards[$atributoId]['valores'][$valorIdOrigen]) ||
            !isset($this->cards[$atributoId]['valores'][$valorIdDestino])
        ) {
            return;
        }

        $valores = $this->cards[$atributoId]['valores'];

        $valorMovido = $valores[$valorIdOrigen];

        unset($valores[$valorIdOrigen]);

        $nuevoOrden = [];

        foreach ($valores as $valorId => $valor) {
            if ($valorId === $valorIdDestino) {
                $nuevoOrden[$valorIdOrigen] = $valorMovido;
            }

            $nuevoOrden[$valorId] = $valor;
        }

        $orden = 1;

        foreach ($nuevoOrden as &$valor) {
            $valor['orden_visual'] = $orden++;
        }

        unset($valor);

        $this->cards[$atributoId]['valores'] = $nuevoOrden;

        Log::info('CATEGORIA ATTRIBUTE VALORES REORDENADOS EN MEMORIA', [
            'categoriaId' => $this->categoriaId,
            'atributoId' => $atributoId,
            'valorIdOrigen' => $valorIdOrigen,
            'valorIdDestino' => $valorIdDestino,
            'valores' => $this->cards[$atributoId]['valores'],
        ]);
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

    /**
     * --------------------------------------------------------------------------
     * Reordenar atributos
     * --------------------------------------------------------------------------
     */
    public function actualizarOrdenAtributos(array $items): void
    {
        foreach ($items as $item) {
            $atributoId = (int) $item['value'];
            $orden = (int) $item['order'];

            if (!isset($this->cards[$atributoId])) {
                continue;
            }

            $this->cards[$atributoId]['orden_visual'] = $orden;
        }

        Log::info('CATEGORIA ATTRIBUTES ORDEN ATRIBUTOS ACTUALIZADO', [
            'categoriaId' => $this->categoriaId,
            'cards' => $this->cards,
        ]);
    }

    /**
     * --------------------------------------------------------------------------
     * Reordenar valores de un atributo
     * --------------------------------------------------------------------------
     */
    public function actualizarOrdenValores(
        int $atributoId,
        array $items
    ): void {
        if (!isset($this->cards[$atributoId])) {
            return;
        }

        foreach ($items as $item) {
            $valorId = (int) $item['value'];
            $orden = (int) $item['order'];

            if (!isset(
                $this->cards[$atributoId]['valores'][$valorId]
            )) {
                continue;
            }

            $this->cards[$atributoId]['valores'][$valorId]['orden_visual'] = $orden;
        }

        Log::info('CATEGORIA ATTRIBUTES ORDEN VALORES ACTUALIZADO', [
            'categoriaId' => $this->categoriaId,
            'atributoId' => $atributoId,
            'cards' => $this->cards,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar atributo de la matriz
    |--------------------------------------------------------------------------
    */

    public function eliminarAtributo(int $atributoId): void
    {
        if (!isset($this->cards[$atributoId])) {
            return;
        }

        $atributo = $this->cards[$atributoId];

        unset($this->cards[$atributoId]);

        /*
        |--------------------------------------------------------------------------
        | El atributo vuelve a estar disponible.
        |--------------------------------------------------------------------------
        */

        $this->loadAvailableAtributos();
        $this->cargarAtributosExistentes();

        /*
        |--------------------------------------------------------------------------
        | Si era el atributo seleccionado en el segundo selector,
        | limpiar esa selección.
        |--------------------------------------------------------------------------
        */

        if (
            $this->selectedExistingAtributoId === $atributoId
        ) {
            $this->selectedExistingAtributoId = null;
            $this->availableExistingValores = [];
            $this->selectedExistingValores = [];
        }

        Log::info('CATEGORIA ATTRIBUTE ELIMINADO EN MEMORIA', [
            'categoriaId' => $this->categoriaId,
            'atributoId' => $atributoId,
            'atributo' => $atributo['atributo'] ?? null,
            'cards' => $this->cards,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar valor de la matriz
    |--------------------------------------------------------------------------
    */

    public function eliminarValor(
        int $atributoId,
        int $valorId
    ): void {
        if (!isset($this->cards[$atributoId])) {
            return;
        }

        if (!isset($this->cards[$atributoId]['valores'][$valorId])) {
            return;
        }

        $valor = $this->cards[$atributoId]['valores'][$valorId];

        unset(
            $this->cards[$atributoId]['valores'][$valorId]
        );

        if (
            (int) $this->selectedExistingAtributoId === (int) $atributoId
        ) {
            $this->updatedSelectedExistingAtributoId($atributoId);
        }

        /*
        |--------------------------------------------------------------------------
        | Si ya no quedan valores, eliminar también el atributo.
        |--------------------------------------------------------------------------
        */

        if (empty($this->cards[$atributoId]['valores'])) {
            unset($this->cards[$atributoId]);

            $this->loadAvailableAtributos();
            $this->cargarAtributosExistentes();

            if (
                $this->selectedExistingAtributoId === $atributoId
            ) {
                $this->selectedExistingAtributoId = null;
                $this->availableExistingValores = [];
                $this->selectedExistingValores = [];
            }
        }

        Log::info('CATEGORIA ATTRIBUTE VALOR ELIMINADO EN MEMORIA', [
            'categoriaId' => $this->categoriaId,
            'atributoId' => $atributoId,
            'valorId' => $valorId,
            'valor' => $valor,
            'cards' => $this->cards,
        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Calcular cambios
    |--------------------------------------------------------------------------
    */

    public function calcularCambios(): array
    {
        $cambios = [
            'atributos_agregados' => [],
            'atributos_eliminados' => [],
            'valores_agregados' => [],
            'valores_eliminados' => [],
        ];

        /*
        |--------------------------------------------------------------------------
        | Atributos agregados / eliminados
        |--------------------------------------------------------------------------
        */

        $atributosOriginales = array_keys(
            $this->originalCards
        );

        $atributosActuales = array_keys(
            $this->cards
        );

        $cambios['atributos_agregados'] = array_values(
            array_diff(
                $atributosActuales,
                $atributosOriginales
            )
        );

        $cambios['atributos_eliminados'] = array_values(
            array_diff(
                $atributosOriginales,
                $atributosActuales
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Valores de atributos nuevos
        |--------------------------------------------------------------------------
        */

        foreach ($cambios['atributos_agregados'] as $atributoId) {
            $valoresActuales = array_keys(
                $this->cards[$atributoId]['valores'] ?? []
            );

            if (!empty($valoresActuales)) {
                $cambios['valores_agregados'][$atributoId] =
                    array_values($valoresActuales);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Valores agregados / eliminados en atributos existentes
        |--------------------------------------------------------------------------
        */

        $atributosComunes = array_intersect(
            $atributosOriginales,
            $atributosActuales
        );

        foreach ($atributosComunes as $atributoId) {
            $valoresOriginales = array_keys(
                $this->originalCards[$atributoId]['valores'] ?? []
            );

            $valoresActuales = array_keys(
                $this->cards[$atributoId]['valores'] ?? []
            );

            $valoresAgregados = array_values(
                array_diff(
                    $valoresActuales,
                    $valoresOriginales
                )
            );

            $valoresEliminados = array_values(
                array_diff(
                    $valoresOriginales,
                    $valoresActuales
                )
            );

            if (!empty($valoresAgregados)) {
                $cambios['valores_agregados'][$atributoId] =
                    $valoresAgregados;
            }

            if (!empty($valoresEliminados)) {
                $cambios['valores_eliminados'][$atributoId] =
                    $valoresEliminados;
            }
        }

        Log::info('CATEGORIA ATTRIBUTES CAMBIOS DEBUG', [
            'categoriaId' => $this->categoriaId,
            'originalCards' => $this->originalCards,
            'cards' => $this->cards,
            'atributosOriginales' => $atributosOriginales,
            'atributosActuales' => $atributosActuales,
            'cambios' => $cambios,
        ]);

        return $cambios;
    }

    /*
    |--------------------------------------------------------------------------
    | Diagnóstico
    |--------------------------------------------------------------------------
    */

    public function diagnosticarCambios(): void
    {
        $this->calcularCambios();

        $this->dispatch('livewire:alert', [
            'message' => 'Diagnóstico de Cambios Realizado',
            'type' => 'success',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar atributos explícitamente
    |--------------------------------------------------------------------------
    */

    #[On('categoria-attributes-cargar')]
    public function cargarCategoriaAtributos(
        ?int $categoriaId = null
    ): void {
        $this->categoriaId = $categoriaId;

        Log::info('cargarCategoriaAtributos', [
            'categoriaId' => $this->categoriaId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Reset de estado
        |--------------------------------------------------------------------------
        */

        $this->cards = [];
        $this->originalCards = [];

        $this->selectedAtributoId = null;
        $this->availableAtributos = [];
        $this->availableValores = [];
        $this->selectedValores = [];

        $this->selectedExistingAtributoId = null;
        $this->availableExistingAtributos = [];
        $this->availableExistingValores = [];
        $this->selectedExistingValores = [];

        if (!$this->categoriaId) {
            $this->categoriaNombre = null;
            $this->show = false;

            return;
        }

        $categoria = Categoria::findOrFail(
            $this->categoriaId
        );

        $this->categoriaNombre = $categoria->nombre;

        $this->loadAttributes();

        $this->show = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Obtener el próximo orden_visual de atributos
    |--------------------------------------------------------------------------
    */

    protected function siguienteOrdenAtributo(): int
    {
        if (empty($this->cards)) {
            return 1;
        }

        return max(
            array_map(
                fn ($card) => (int) ($card['orden_visual'] ?? 0),
                $this->cards
            )
        ) + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Obtener el próximo orden_visual de valores
    |--------------------------------------------------------------------------
    */
    protected function siguienteOrdenValor(int $atributoId): int
    {
        $valores = $this->cards[$atributoId]['valores'] ?? [];

        if (empty($valores)) {
            return 1;
        }

        return max(
            array_column($valores, 'orden_visual')
        ) + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Cargar matriz de atributos
    |--------------------------------------------------------------------------
    */

    protected function loadAttributes(): void
    {
        $this->cards = [];

        if (!$this->categoriaId) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Atributos asociados a la categoría
        |--------------------------------------------------------------------------
        */

        $atributables = AtributableAtributo::with('atributo')
            ->where(
                'atributable_type',
                Categoria::class
            )
            ->where(
                'atributable_id',
                $this->categoriaId
            )
            ->orderBy('orden_visual')
            ->get();

        foreach ($atributables as $item) {
            if (!$item->atributo) {
                continue;
            }

            $attrId = (int) $item->atributo->id;

            $this->cards[$attrId] = [
                'atributo' => $item->atributo->nombre,
                'orden_visual' => (int) $item->orden_visual,
                'valores' => [],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Valores asociados a la categoría
        |--------------------------------------------------------------------------
        */

        $valores = AtributableValor::with('atributoValor')
            ->where(
                'atributable_type',
                Categoria::class
            )
            ->where(
                'atributable_id',
                $this->categoriaId
            )
            ->orderBy('orden_visual')
            ->get();

        foreach ($valores as $item) {
            if (!$item->atributoValor) {
                continue;
            }

            $atributoValor = $item->atributoValor;

            $attrId = (int) $atributoValor->atributo_id;

            if (!isset($this->cards[$attrId])) {
                continue;
            }

            $this->cards[$attrId]['valores'][
                (int) $atributoValor->id
            ] = [
                'valor' => $atributoValor->valor,
                'orden_visual' => (int) $item->orden_visual,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Snapshot del estado persistido
        |--------------------------------------------------------------------------
        */

        $this->originalCards = $this->cards;

        Log::info('CATEGORIA ATTRIBUTES SNAPSHOT', [
            'categoriaId' => $this->categoriaId,
            'originalCards' => $this->originalCards,
            'cards' => $this->cards,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Catálogos
        |--------------------------------------------------------------------------
        */

        $this->loadAvailableAtributos();

        $this->cargarAtributosExistentes();

        Log::info('CATEGORIA ATTRIBUTES LOAD', [
            'categoriaId' => $this->categoriaId,
            'cards' => $this->cards,
            'availableAtributos' => $this->availableAtributos,
            'availableExistingAtributos' =>
                $this->availableExistingAtributos,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Atributos disponibles para agregar
    |--------------------------------------------------------------------------
    */

    protected function loadAvailableAtributos(): void
    {
        $atributosUsados = array_map(
            'intval',
            array_keys($this->cards)
        );

        $this->availableAtributos = Atributo::query()
            ->when(
                !empty($atributosUsados),
                fn ($query) =>
                    $query->whereNotIn(
                        'id',
                        $atributosUsados
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
    | Atributos actualmente presentes en la matriz
    |--------------------------------------------------------------------------
    */

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
    | Selección de atributo nuevo
    |--------------------------------------------------------------------------
    */

    public function updatedSelectedAtributoId(
        $atributoId
    ): void {
        $this->availableValores = [];
        $this->selectedValores = [];

        if (!$atributoId) {
            return;
        }

        $atributoId = (int) $atributoId;

        $atributo = Atributo::with('valores')
            ->find($atributoId);

        if (!$atributo) {
            return;
        }

        $valoresAsociados = array_keys(
            $this->cards[$atributo->id]['valores'] ?? []
        );

        $valoresAsociados = array_map(
            'intval',
            $valoresAsociados
        );

        $this->availableValores = $atributo->valores
            ->sortBy([
                ['orden_visual', 'asc'],
                ['valor', 'asc'],
            ])
            ->mapWithKeys(function ($valor) use (
                $valoresAsociados
            ) {
                return [
                    $valor->id => [
                        'valor' => $valor->valor,
                        'asociado' => in_array(
                            (int) $valor->id,
                            $valoresAsociados,
                            true
                        ),
                    ],
                ];
            })
            ->toArray();

        Log::info('CATEGORIA ATTRIBUTE SELECTED', [
            'categoriaId' => $this->categoriaId,
            'atributoId' => $atributo->id,
            'atributo' => $atributo->nombre,
            'valoresAsociados' => $valoresAsociados,
            'valores' => $this->availableValores,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Selección de atributo existente
    |--------------------------------------------------------------------------
    */

    public function updatedSelectedExistingAtributoId(
        $atributoId
    ): void {
        $this->availableExistingValores = [];
        $this->selectedExistingValores = [];

        if (!$atributoId) {
            return;
        }

        $atributoId = (int) $atributoId;

        /*
        |--------------------------------------------------------------------------
        | El atributo debe existir actualmente en la matriz.
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

        $valoresAsociados = array_keys(
            $this->cards[$atributoId]['valores'] ?? []
        );

        $valoresAsociados = array_map(
            'intval',
            $valoresAsociados
        );

        $this->availableExistingValores = $atributo->valores
            ->sortBy([
                ['orden_visual', 'asc'],
                ['valor', 'asc'],
            ])
            ->mapWithKeys(function ($valor) use (
                $valoresAsociados
            ) {
                $asociado = in_array(
                    (int) $valor->id,
                    $valoresAsociados,
                    true
                );

                return [
                    $valor->id => [
                        'valor' => $valor->valor,
                        'asociado' => $asociado,
                    ],
                ];
            })
            ->toArray();

        Log::info(
            'CATEGORIA ATTRIBUTE EXISTENTE SELECTED',
            [
                'categoriaId' => $this->categoriaId,
                'atributoId' => $atributo->id,
                'atributo' => $atributo->nombre,
                'valoresAsociados' => $valoresAsociados,
                'valores' => $this->availableExistingValores,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cerrar modal
    |--------------------------------------------------------------------------
    */

    public function cerrar(): void
    {
        $this->show = false;

        $this->categoriaId = null;
        $this->categoriaNombre = null;

        $this->cards = [];
        $this->originalCards = [];

        $this->selectedAtributoId = null;
        $this->availableAtributos = [];
        $this->availableValores = [];
        $this->selectedValores = [];

        $this->selectedExistingAtributoId = null;
        $this->availableExistingAtributos = [];
        $this->availableExistingValores = [];
        $this->selectedExistingValores = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        return view(
            'modules.inventario.categoria.attributes-manager'
        );
    }
}