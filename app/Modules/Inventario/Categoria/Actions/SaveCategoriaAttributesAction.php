<?php

namespace App\Modules\Inventario\Categoria\Actions;

use App\Models\AtributableAtributo;
use App\Models\AtributableValor;
use App\Models\Atributo;
use App\Models\AtributoValor;
use App\Models\Categoria;
use App\Modules\Inventario\Categoria\DTOs\SaveCategoriaAttributesDTO;
use Illuminate\Support\Facades\DB;

class SaveCategoriaAttributesAction
{
    public function execute(
        SaveCategoriaAttributesDTO $dto
    ): void {
        DB::transaction(function () use ($dto) {

            /*
             * --------------------------------------------------------------------------
             * 1. Validar categoría
             * --------------------------------------------------------------------------
             */

            $categoria = Categoria::query()
                ->findOrFail($dto->categoriaId);

            /*
             * --------------------------------------------------------------------------
             * 2. Normalizar matriz
             * --------------------------------------------------------------------------
             *
             * $cards representa el estado final deseado.
             *
             * Los nombres son informativos.
             * Los IDs determinan las relaciones.
             *
             * Conservamos también orden_visual porque forma parte
             * del estado que queremos persistir.
             */

            $cardsNormalizadas = [];

            foreach ($dto->cards as $atributoId => $card) {

                $atributoId = (int) $atributoId;

                /*
                 * El atributo debe existir.
                 */

                $atributo = Atributo::query()
                    ->find($atributoId);

                if (!$atributo) {
                    continue;
                }

                /*
                 * ----------------------------------------------------------------------
                 * Valores
                 * ----------------------------------------------------------------------
                 */

                $valores = [];

                foreach (($card['valores'] ?? []) as $valorId => $valorData) {

                    $valorId = (int) $valorId;

                    /*
                     * El valor debe existir y pertenecer al atributo.
                     */

                    $valorExiste = AtributoValor::query()
                        ->whereKey($valorId)
                        ->where(
                            'atributo_id',
                            $atributoId
                        )
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

                /*
                 * Un atributo sin valores no forma parte del estado
                 * persistible.
                 */

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
             * --------------------------------------------------------------------------
             * 3. IDs finales de atributos
             * --------------------------------------------------------------------------
             */

            $atributosActuales = array_map(
                'intval',
                array_keys($cardsNormalizadas)
            );

            /*
             * --------------------------------------------------------------------------
             * 4. Eliminar atributos que ya no están en la matriz
             * --------------------------------------------------------------------------
             *
             * Esto cubre:
             *
             * - eliminación manual de un atributo
             * - eliminación de todos los valores de un atributo
             * - eliminación de todos los atributos
             */

            $atributosQuery = AtributableAtributo::query()
                ->where(
                    'atributable_type',
                    Categoria::class
                )
                ->where(
                    'atributable_id',
                    $categoria->id
                );

            if (empty($atributosActuales)) {

                $atributosQuery->delete();

            } else {

                $atributosQuery
                    ->whereNotIn(
                        'atributo_id',
                        $atributosActuales
                    )
                    ->delete();
            }

            /*
             * --------------------------------------------------------------------------
             * 5. IDs finales de valores
             * --------------------------------------------------------------------------
             */

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
             * --------------------------------------------------------------------------
             * 6. Eliminar valores que ya no están en la matriz
             * --------------------------------------------------------------------------
             *
             * Esto cubre:
             *
             * - valor eliminado individualmente
             * - valores de atributo eliminado
             * - eliminación completa de atributos
             */

            $valoresQuery = AtributableValor::query()
                ->where(
                    'atributable_type',
                    Categoria::class
                )
                ->where(
                    'atributable_id',
                    $categoria->id
                );

            if (empty($valoresActuales)) {

                $valoresQuery->delete();

            } else {

                $valoresQuery
                    ->whereNotIn(
                        'atributo_valor_id',
                        $valoresActuales
                    )
                    ->delete();
            }

            /*
             * --------------------------------------------------------------------------
             * 7. Persistir atributos y orden
             * --------------------------------------------------------------------------
             *
             * El orden de la matriz determina el orden visual.
             *
             * Usamos el orden_visual que ya viene en $cards.
             */

            foreach (
                $cardsNormalizadas as $atributoId => $card
            ) {

                AtributableAtributo::updateOrCreate(
                    [
                        'atributo_id' => $atributoId,
                        'atributable_type' => Categoria::class,
                        'atributable_id' => $categoria->id,
                    ],
                    [
                        'orden_visual' => $card['orden_visual'],
                    ]
                );
            }

            /*
             * --------------------------------------------------------------------------
             * 8. Persistir valores y orden
             * --------------------------------------------------------------------------
             *
             * Cada atributo tiene su propio orden_visual comenzando en 1.
             *
             * La matriz ya contiene el orden correcto, pero normalizamos
             * nuevamente la posición para garantizar consistencia.
             */

            foreach (
                $cardsNormalizadas as $atributoId => $card
            ) {

                $ordenValor = 1;

                foreach (
                    $card['valores'] as $valorId => $valorData
                ) {

                    AtributableValor::updateOrCreate(
                        [
                            'atributo_valor_id' => $valorId,
                            'atributable_type' => Categoria::class,
                            'atributable_id' => $categoria->id,
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