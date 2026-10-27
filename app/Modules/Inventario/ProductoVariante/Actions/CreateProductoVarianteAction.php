<?php

namespace App\Modules\Inventario\ProductoVariante\Actions;

use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Modules\Inventario\ProductoVariante\DTOs\CreateProductoVarianteDTO;
use App\Modules\Inventario\ProductoVariante\Services\GeneradorClaveCombinacion;
use App\Modules\Inventario\ProductoVariante\Services\ValidarValoresProductoVarianteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateProductoVarianteAction
{
    public function __construct(
        private readonly GeneradorClaveCombinacion $generadorClave,
        private readonly ValidarValoresProductoVarianteService $validadorValores,
    ) {
    }

    public function __invoke(
        CreateProductoVarianteDTO $dto
    ): ProductoVariante {
        return DB::transaction(function () use ($dto): ProductoVariante {
            $producto = Producto::query()
                ->lockForUpdate()
                ->findOrFail($dto->productoId);

            $valores = $this->validadorValores->validar(
                $producto,
                $dto->atributosValores,
            );

            $clave = $this->generadorClave->generar(
                producto: $producto,
                valores: $valores,
            );

            $combinacionExiste = ProductoVariante::query()
                ->where('producto_id', $producto->id)
                ->where('clave_combinacion', $clave)
                ->exists();

            if ($combinacionExiste) {
                throw ValidationException::withMessages([
                    'atributosValores' =>
                        'Ya existe una variante de este producto con esa combinación.',
                ]);
            }

            $variante = ProductoVariante::query()->create([
                'producto_id' => $producto->id,
                'clave_combinacion' => $clave,
                'nombre' => $dto->nombre,
                'activo' => $dto->activo,
            ]);

            $variante->atributosValores()->sync(
                $valores->modelKeys()
            );

            return $variante->load('atributosValores.atributo');
        });
    }
}