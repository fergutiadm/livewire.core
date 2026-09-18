<?php

namespace App\Modules\Inventario\Atributo\Actions;

use App\Models\Atributo;
use App\Modules\Inventario\Atributo\DTOs\UpdateAtributoDTO;
use App\Modules\Inventario\Atributo\Models\AtributoValor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateAtributoAction
{
    public function execute(UpdateAtributoDTO $dto): Atributo
    {
        return DB::transaction(function () use ($dto) {
            $atributo = Atributo::query()
                ->lockForUpdate()
                ->findOrFail($dto->id);

            $this->validateDatos($dto);

            $data = [
                'codigo' => $dto->codigo,
                'nombre' => $dto->nombre,
                'descripcion' => $dto->descripcion,
                'orden_visual' => $dto->orden_visual,
            ];

            if ($dto->activo !== null) {
                $data['activo'] = $dto->activo;
            }

            $atributo->update($data);

            /*
             * NULL significa que esta operación no modifica
             * la colección de valores.
             *
             * Esto ocurre al activar/desactivar desde la tabla.
             */
            if ($dto->valores !== null) {
                $this->sincronizarValores(
                    $atributo,
                    $dto->valores
                );
            }

            return $atributo->load([
                'valores' => function ($query) {
                    $query
                        ->where('activo', true)
                        ->orderBy('orden_visual');
                },
            ]);
        });
    }

    private function validateDatos(UpdateAtributoDTO $dto): void
    {
        if ($dto->codigo === '') {
            throw ValidationException::withMessages([
                'codigo' =>
                    'El código del atributo es obligatorio.',
            ]);
        }

        if ($dto->nombre === '') {
            throw ValidationException::withMessages([
                'nombre' =>
                    'El nombre del atributo es obligatorio.',
            ]);
        }

        if ($dto->valores !== null) {
            $this->validateCodigosValores(
                $dto->valores
            );
        }
    }

    private function validateCodigosValores(array $valores): void
    {
        $codigos = [];

        foreach ($valores as $valor) {
            $codigo = mb_strtoupper(
                trim((string) ($valor['codigo'] ?? ''))
            );

            $nombre = trim(
                (string) ($valor['nombre'] ?? '')
            );

            if ($codigo === '') {
                throw ValidationException::withMessages([
                    'valores' =>
                        'Todos los valores deben tener código.',
                ]);
            }

            if ($nombre === '') {
                throw ValidationException::withMessages([
                    'valores' =>
                        'Todos los valores deben tener nombre.',
                ]);
            }

            if (isset($codigos[$codigo])) {
                throw ValidationException::withMessages([
                    'valores' =>
                        "El código de valor '{$codigo}' está repetido.",
                ]);
            }

            $codigos[$codigo] = true;
        }
    }

    private function sincronizarValores(
        Atributo $atributo,
        array $valores
    ): void {
        $idsActivos = [];

        foreach ($valores as $index => $valor) {
            $ordenVisual =
                $valor['orden_visual'] ?? ($index + 1);

            /*
             * Valor existente.
             */
            if (!empty($valor['id'])) {
                $atributoValor = AtributoValor::query()
                    ->where('id', (int) $valor['id'])
                    ->where(
                        'atributo_id',
                        $atributo->id
                    )
                    ->lockForUpdate()
                    ->first();

                if (!$atributoValor) {
                    throw ValidationException::withMessages([
                        'valores' => sprintf(
                            'El valor con ID %d no pertenece al atributo.',
                            $valor['id']
                        ),
                    ]);
                }

                $atributoValor->update([
                    'codigo' => mb_strtoupper(
                        trim((string) $valor['codigo'])
                    ),
                    'nombre' => trim(
                        (string) $valor['nombre']
                    ),
                    'orden_visual' => $ordenVisual,
                    'activo' => true,
                ]);

                $idsActivos[] = $atributoValor->id;

                continue;
            }

            /*
             * Valor nuevo.
             */
            $atributoValor = $atributo->valores()->create([
                'codigo' => mb_strtoupper(
                    trim((string) $valor['codigo'])
                ),
                'nombre' => trim(
                    (string) $valor['nombre']
                ),
                'orden_visual' => $ordenVisual,
                'activo' => true,
            ]);

            $idsActivos[] = $atributoValor->id;
        }

        /*
         * Los valores anteriores que desaparecieron del
         * formulario pasan a inactivos.
         */
        $query = AtributoValor::query()
            ->where('atributo_id', $atributo->id);

        if ($idsActivos !== []) {
            $query->whereNotIn('id', $idsActivos);
        }

        $query->update([
            'activo' => false,
        ]);
    }
}