<?php

namespace App\Modules\Inventario\Atributo\Actions;

use App\Models\Atributo;
use App\Modules\Inventario\Atributo\DTOs\CreateAtributoDTO;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateAtributoAction
{
    public function execute(CreateAtributoDTO $dto): Atributo
    {
        return DB::transaction(function () use ($dto) {
            $this->validateDatos($dto);

            $ordenVisual = $dto->orden_visual;

            if ($ordenVisual === null) {
                $ordenVisual = ((int) Atributo::max('orden_visual')) + 1;
            }

            $atributo = Atributo::create([
                'codigo' => $dto->codigo,
                'nombre' => $dto->nombre,
                'descripcion' => $dto->descripcion,
                'orden_visual' => $ordenVisual,
                'activo' => true,
            ]);

            foreach ($dto->valores as $index => $valor) {
                $atributo->valores()->create([
                    'codigo' => $valor['codigo'],
                    'nombre' => $valor['nombre'],
                    'orden_visual' => $valor['orden_visual'] ?? ($index + 1),
                    'activo' => true,
                ]);
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

    private function validateDatos(CreateAtributoDTO $dto): void
    {
        if ($dto->codigo === '') {
            throw ValidationException::withMessages([
                'codigo' => 'El código del atributo es obligatorio.',
            ]);
        }

        if ($dto->nombre === '') {
            throw ValidationException::withMessages([
                'nombre' => 'El nombre del atributo es obligatorio.',
            ]);
        }

        $this->validateCodigosValores($dto->valores);
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
                    'valores' => 'Todos los valores deben tener código.',
                ]);
            }

            if ($nombre === '') {
                throw ValidationException::withMessages([
                    'valores' => 'Todos los valores deben tener nombre.',
                ]);
            }

            if (isset($codigos[$codigo])) {
                throw ValidationException::withMessages([
                    'valores' => "El código de valor '{$codigo}' está repetido.",
                ]);
            }

            $codigos[$codigo] = true;
        }
    }
}