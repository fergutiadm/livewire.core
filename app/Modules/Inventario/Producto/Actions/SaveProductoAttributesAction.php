<?php

namespace App\Modules\Inventario\Producto\Actions;

use App\Models\AtributableAtributo;
use App\Models\AtributableValor;
use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Modules\Inventario\Producto\DTOs\SaveProductoAttributesDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveProductoAttributesAction
{
    public function execute(
        SaveProductoAttributesDTO $dto
    ): void {
        DB::transaction(function () use ($dto) {

            /*
             * 1. Validar y bloquear el producto.
             */
            $producto = Producto::query()
                ->lockForUpdate()
                ->findOrFail($dto->productoId);

            /*
             * 2. Normalizar la matriz final.
             */
            $cardsNormalizadas = [];

            foreach ($dto->cards as $atributoId => $card) {
                $atributoId = (int) $atributoId;

                $atributo = Atributo::query()
                    ->find($atributoId);

                if (!$atributo) {
                    continue;
                }

                $valores = [];

                foreach (($card['valores'] ?? []) as $valorId => $valorData) {
                    $valorId = (int) $valorId;

                    $valorExiste = AtributoValor::query()
                        ->whereKey($valorId)
                        ->where('atributo_id', $atributoId)
                        ->exists();

                    if (!$valorExiste) {
                        continue;
                    }

                    $valores[$valorId] = [
                        'valor' => $valorData['valor'] ?? null,
                        'orden_visual' => (int) (
                            $valorData['orden_visual'] ?? 0
                        ),
                    ];
                }

                if (empty($valores)) {
                    continue;
                }

                $cardsNormalizadas[$atributoId] = [
                    'atributo' => $card['atributo'] ?? $atributo->nombre,
                    'orden_visual' => (int) (
                        $card['orden_visual'] ?? 0
                    ),
                    'valores' => $valores,
                ];
            }

            /*
             * 3. Obtener los IDs del estado final.
             */
            $atributosActuales = array_map(
                'intval',
                array_keys($cardsNormalizadas)
            );

            $valoresActuales = [];

            foreach ($cardsNormalizadas as $card) {
                foreach ($card['valores'] as $valorId => $valorData) {
                    $valoresActuales[] = (int) $valorId;
                }
            }

            $valoresActuales = array_values(
                array_unique($valoresActuales)
            );

            /*
             * 4. Identificar atributos que se eliminarían.
             */
            $atributosAsignados = AtributableAtributo::query()
                ->where('atributable_type', Producto::class)
                ->where('atributable_id', $producto->id)
                ->pluck('atributo_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $atributosEliminados = array_values(
                array_diff($atributosAsignados, $atributosActuales)
            );

            /*
             * 5. Proteger atributos utilizados por variantes.
             */
            if (!empty($atributosEliminados)) {
                $atributoEnUso = ProductoVariante::query()
                    ->where('producto_id', $producto->id)
                    ->whereHas('atributosValores', function ($query) use (
                        $atributosEliminados
                    ) {
                        $query->whereIn(
                            'atributos_valores.atributo_id',
                            $atributosEliminados
                        );
                    })
                    ->exists();

                if ($atributoEnUso) {
                    throw ValidationException::withMessages([
                        'cards' =>
                            'No se puede quitar un atributo utilizado por una o más variantes del producto.',
                    ]);
                }
            }

            /*
             * 6. Proteger valores utilizados por variantes.
             *
             * Si alguna variante utiliza un valor que no estará
             * asignado al producto después de guardar, se cancela
             * toda la transacción.
             */
            $varianteConValorEliminado = ProductoVariante::query()
                ->where('producto_id', $producto->id)
                ->whereHas('atributosValores', function ($query) use (
                    $valoresActuales
                ) {
                    if (!empty($valoresActuales)) {
                        $query->whereNotIn(
                            'atributos_valores.id',
                            $valoresActuales
                        );
                    }
                })
                ->exists();

            if ($varianteConValorEliminado) {
                throw ValidationException::withMessages([
                    'cards' =>
                        'No se puede quitar un valor de atributo utilizado por una o más variantes del producto.',
                ]);
            }

            /*
             * 7. Eliminar atributos que ya no están en la matriz.
             */
            $atributosQuery = AtributableAtributo::query()
                ->where('atributable_type', Producto::class)
                ->where('atributable_id', $producto->id);

            if (empty($atributosActuales)) {
                $atributosQuery->delete();
            } else {
                $atributosQuery
                    ->whereNotIn('atributo_id', $atributosActuales)
                    ->delete();
            }

            /*
             * 8. Eliminar valores que ya no están en la matriz.
             */
            $valoresQuery = AtributableValor::query()
                ->where('atributable_type', Producto::class)
                ->where('atributable_id', $producto->id);

            if (empty($valoresActuales)) {
                $valoresQuery->delete();
            } else {
                $valoresQuery
                    ->whereNotIn('atributo_valor_id', $valoresActuales)
                    ->delete();
            }

            /*
             * 9. Persistir atributos y orden.
             */
            foreach ($cardsNormalizadas as $atributoId => $card) {
                AtributableAtributo::updateOrCreate(
                    [
                        'atributo_id' => $atributoId,
                        'atributable_type' => Producto::class,
                        'atributable_id' => $producto->id,
                    ],
                    [
                        'orden_visual' => $card['orden_visual'],
                    ]
                );
            }

            /*
             * 10. Persistir valores y orden.
             */
            foreach ($cardsNormalizadas as $card) {
                $ordenValor = 1;

                foreach ($card['valores'] as $valorId => $valorData) {
                    AtributableValor::updateOrCreate(
                        [
                            'atributo_valor_id' => $valorId,
                            'atributable_type' => Producto::class,
                            'atributable_id' => $producto->id,
                        ],
                        [
                            'orden_visual' => $ordenValor,
                        ]
                    );

                    $ordenValor++;
                }
            }
        });
    }
}