<?php

namespace App\Http\Livewire;

use App\Models\AtributableValor;
use App\Models\AtributableAtributo;
use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\On;

class SelectAtributoCardsProxy extends Component
{
    public string $modelType;
    public int $modelId;
    public bool $atributosSugeridos = false;
    public array $cards = [];
    public bool $cardsDirty = false;

    public bool $cerrarModalAlSalvar = true;

    public function mount(string $modelType, int $modelId)
    {
        $this->modelType = $modelType;
        $this->modelId = $modelId;

        $this->cards = $this->cargarCardsDesdeBD();
        $this->cardsDirty = false;

        $this->dispatch('cards-dirty-updated', dirty: $this->cardsDirty);
        $this->dispatch('atributos-sugeridos-updated', sugeridos: $this->atributosSugeridos);
    }

    /**
     * Carga atributos y valores desde la BD
     */
    protected function cargarCardsDesdeBD(): array
    {
        $this->atributosSugeridos = false;
        $cards = [];
        $modelClass = $this->getModelClass($this->modelType);
        if (!$modelClass) return $cards;

        // 1️⃣ Cargar atributos asociados al modelo
        $atributables = AtributableAtributo::with('atributo')
            ->where('atributable_type', $modelClass)
            ->where('atributable_id', $this->modelId)
            ->orderBy('orden_visual')
            ->get();

        foreach ($atributables as $item) {
            $attrId = $item->atributo->id;
            $cards[$attrId] = [
                'atributo' => $item->atributo->nombre,
                'valores'  => [],
                'sugerido' => false,
            ];
        }

        // 2️⃣ Cargar valores asociados
        $valores = AtributableValor::with('atributoValor')
            ->where('atributable_type', $modelClass)
            ->where('atributable_id', $this->modelId)
            ->get();

        foreach ($valores as $item) {
            $attrId = $item->atributoValor->atributo_id;
            if (isset($cards[$attrId])) {
                $cards[$attrId]['valores'][$item->atributoValor->id] = $item->atributoValor->valor;
            }
        }

        Logger('dump($cards)',['cards' => $cards]);
        // 3️⃣ Sugerir atributos de categoría si es producto y no tiene atributos propios
        if ($this->modelType === 'producto' && empty($cards)) {
            Logger('Sugerir atributos de categoría si es producto y no tiene atributos propios');
            $producto = Producto::find($this->modelId);
            if ($producto && $producto->categoria_id) {
                $atributosCategoria = AtributableAtributo::with('atributo')
                    ->where('atributable_type', \App\Models\Categoria::class)
                    ->where('atributable_id', $producto->categoria_id)
                    ->orderBy('orden_visual')
                    ->get();

                foreach ($atributosCategoria as $item) {
                    $attrId = $item->atributo->id;
                    $cards[$attrId] = [
                        'atributo' => $item->atributo->nombre,
                        'valores'  => [], // aún sin valores
                    ];

                    // 2️⃣ Cargar valores asociados
                    $valores = AtributableValor::with('atributoValor')
                        ->where('atributable_type', \App\Models\Categoria::class)
                        ->where('atributable_id', $producto->categoria_id)
                        ->get();

                    foreach ($valores as $item) {
                        $attrId = $item->atributoValor->atributo_id;
                        if (isset($cards[$attrId])) {
                            $cards[$attrId]['valores'][$item->atributoValor->id] = $item->atributoValor->valor;
                        }
                    }
                    if(!empty($valores)){
                        $cards[$attrId]['sugerido'] = true;
                    }
                }

                $this->atributosSugeridos = !empty($cards);
            }
        }elseif ($this->modelType === 'producto' && !empty($cards)) {
            $producto = Producto::find($this->modelId);

            Logger('Cargar valores para $attrId: '.$attrId);
            foreach($cards as $attrId => $valores){
                if(empty($cards[$attrId]['valores'])){
                    Logger('Cargar valores para $attrId: '.$attrId);
                    // 1️⃣ Cargar valores asociados
                    $valores = AtributableValor::with('atributoValor')
                        ->where('atributable_type', \App\Models\Categoria::class)
                        ->where('atributable_id', $producto->categoria_id)
                        ->get();

                    foreach ($valores as $item) {
                        $_attrId = $item->atributoValor->atributo_id;
                        if (isset($cards[$_attrId])) {
                            $cards[$_attrId]['valores'][$item->atributoValor->id] = $item->atributoValor->valor;
                        }
                    }
                    if(!empty($valores)){
                        $cards[$_attrId]['sugerido'] = true;
                    }
                    $this->atributosSugeridos = true;
                }
            }
        }

        $this->dispatch('atributos-sugeridos-updated', sugeridos: $this->atributosSugeridos);

        return $cards;
    }

    private function getModelClass(string $modelType): ?string
    {
        return match ($modelType) {
            'producto' => \App\Models\Producto::class,
            'categoria' => \App\Models\Categoria::class,
            default => null,
        };
    }

    #[On('cards-updated')]
    public function actualizarCards(array $cards): void
    {
        $this->cards = $cards;
        $this->marcarDirty(true);
    }

    public function eliminarCard(int $atributoId)
    {
        unset($this->cards[$atributoId]);
        $this->marcarDirty(true);
    }

    public function quitarValor(int $atributoId, int $valorId)
    {
        if (isset($this->cards[$atributoId]['valores'][$valorId])) {
            unset($this->cards[$atributoId]['valores'][$valorId]);
        }
        $this->marcarDirty(true);
    }

    /**
     * Guardar atributos y valores en la BD
     */
    public function guardarCardsOrden()
    {
        $modelClass = $this->getModelClass($this->modelType);
        $ordenAttr = 0;

        foreach ($this->cards as $attrId => $card) {
            // Guardar atributo
            AtributableAtributo::updateOrCreate(
                [
                    'atributo_id' => $attrId,
                    'atributable_type' => $modelClass,
                    'atributable_id' => $this->modelId,
                ],
                ['orden_visual' => $ordenAttr++]
            );

            // Guardar valores
            $ordenVal = 0;
            $idsValores = [];
            foreach ($card['valores'] as $valorId => $valor) {
                AtributableValor::updateOrCreate(
                    [
                        'atributo_valor_id' => $valorId,
                        'atributable_type' => $modelClass,
                        'atributable_id' => $this->modelId,
                    ],
                    ['orden_visual' => $ordenVal++]
                );
                $c = $valorId;
                $idsValores[] = $valorId;
            }

            // Valores asociados
            $valores = AtributableValor::with('atributoValor')
                ->where('atributable_type', $modelClass)
                ->where('atributable_id', $this->modelId)
                ->get();

            foreach ($valores as $item) {
                $attrId = $item->atributoValor->atributo_id;
                if (!isset($idsValores[$attrId])) {
                    Logger('Eliminando registro de atributable_valor $valores as $item',[
                        'item' => $item
                    ]);
                    Logger('Eliminando registro de atributable_valor',[
                        'attrId' => $attrId,
                        'atributo_id' => $item->atributo_id,
                        'id' => $item->id,
                        'atributo_valor_id' => $item->atributo_valor_id,
                        'atributo_valor_type' => $item->atributo_valor_type,
                    ]);
                    //$item->delete();
                }
            }
        }

        // Si había atributos sugeridos, marcarlos como parte del producto
        if ($this->atributosSugeridos) {
            $this->atributosSugeridos = false;
            $this->dispatch('livewire:alert', [
                'message' => 'Los atributos sugeridos ahora forman parte del producto',
                'type' => 'success',
            ]);
            $this->dispatch('atributos-sugeridos-updated', sugeridos: false);
        }

        $this->marcarDirty(false);

        $this->dispatch('livewire:alert', [
            'message' => 'Atributos y valores guardados correctamente',
            'type' => 'success',
        ]);

        if($this->cerrarModalAlSalvar){
            $this->dispatch('cerrar-modal-edit-atributos');
        }
    }

    /**
     * Reordenar los valores de un card
     */
    public function reorderValores(array $sortableItems)
    {
        if (empty($sortableItems)) return;

        // Tomamos atributoId del primer item
        [$atributoId] = explode(':', $sortableItems[0]['value']);
        $atributoId = (int) $atributoId;

        // Extraer IDs de valores
        $valoresIds = collect($sortableItems)
            ->map(fn($item) => (int) explode(':', $item['value'])[1])
            ->toArray();

        // Actualizar orden en BD
        $atributables = AtributableValor::where('atributable_type', $this->getModelClass($this->modelType))
            ->where('atributable_id', $this->modelId)
            ->whereIn('atributo_valor_id', $valoresIds)
            ->get()
            ->keyBy('atributo_valor_id');

        foreach ($valoresIds as $index => $valorId) {
            if (isset($atributables[$valorId])) {
                $atributables[$valorId]->orden_visual = $index;
                $atributables[$valorId]->save();
            }
        }

        // Reordenar en memoria
        $valoresActuales = $this->cards[$atributoId]['valores'];
        $nuevoOrden = [];
        foreach ($valoresIds as $valorId) {
            $nuevoOrden[$valorId] = $valoresActuales[$valorId];
        }
        $this->cards[$atributoId]['valores'] = $nuevoOrden;

        $this->marcarDirty(true);
    }

    /**
     * Reordenar los cards completos
     */
    public function reorderCards(array $nuevoOrden)
    {
        $cardsReordenadas = [];
        foreach ($nuevoOrden as $item) {
            $attrId = (int) $item['value']; // <-- extraer value como int
            if (isset($this->cards[$attrId])) {
                $cardsReordenadas[$attrId] = $this->cards[$attrId];
            }
        }

        $this->cards = $cardsReordenadas;
        $this->marcarDirty(true);

        // Actualizar orden_visual en BD
        $modelClass = $this->getModelClass($this->modelType);
        foreach (array_keys($this->cards) as $index => $attrId) {
            AtributableAtributo::where([
                'atributo_id' => $attrId,
                'atributable_type' => $modelClass,
                'atributable_id' => $this->modelId,
            ])->update(['orden_visual' => $index]);
        }
    }

    private function marcarDirty(bool $dirty)
    {
        $this->cardsDirty = $dirty;
        $this->dispatch('cards-dirty-updated', dirty: $dirty);
    }

    public function cambiarCerrarModalAlSalvar()
    {
        $this->cerrarModalAlSalvar = !$this->cerrarModalAlSalvar;
    }

    public function render()
    {
        return view('livewire.select-atributo-cards-proxy');
    }
}
