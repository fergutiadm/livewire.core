<?php

namespace App\Modules\Inventario\ProductoVariante\Actions;

use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Modules\Inventario\ProductoVariante\DTOs\UpdateProductoVarianteDTO;
use App\Modules\Inventario\ProductoVariante\Services\GeneradorClaveCombinacion;
use App\Modules\Inventario\ProductoVariante\Services\ValidarValoresProductoVarianteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateProductoVarianteAction
{
    public function __construct(
        private readonly GeneradorClaveCombinacion $generadorClave,
        private readonly ValidarValoresProductoVarianteService $validadorValores,
    ) {
    }

    public function __invoke(
        UpdateProductoVarianteDTO $dto
    ): ProductoVariante {
        return DB::transaction(function () use ($dto): ProductoVariante {

            /*
            |--------------------------------------------------------------------------
            | 1. Identificar la variante y su producto
            |--------------------------------------------------------------------------
            */

            $varianteInicial = ProductoVariante::query()
                ->findOrFail($dto->id);

            /*
            |--------------------------------------------------------------------------
            | 2. Bloquear el producto y la variante
            |--------------------------------------------------------------------------
            */

            $producto = Producto::query()
                ->lockForUpdate()
                ->findOrFail($varianteInicial->producto_id);

            $variante = ProductoVariante::query()
                ->where('producto_id', $producto->id)
                ->lockForUpdate()
                ->findOrFail($dto->id);

            /*
            |--------------------------------------------------------------------------
            | 3. Validar los valores seleccionados
            |--------------------------------------------------------------------------
            */

            $valores = $this->validadorValores->validar(
                $producto,
                $dto->atributosValores,
            );

            /*
            |--------------------------------------------------------------------------
            | 4. Generar la clave canónica
            |--------------------------------------------------------------------------
            */

            $clave = $this->generadorClave->generar(
                producto: $producto,
                valores: $valores,
            );

            /*
            |--------------------------------------------------------------------------
            | 5. Evitar combinaciones duplicadas
            |--------------------------------------------------------------------------
            */

            $combinacionExiste = ProductoVariante::query()
                ->where('producto_id', $producto->id)
                ->where('clave_combinacion', $clave)
                ->where('id', '<>', $variante->id)
                ->exists();

            if ($combinacionExiste) {
                throw ValidationException::withMessages([
                    'atributosValores' =>
                        'Ya existe otra variante de este producto con esa combinación.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Actualizar la variante
            |--------------------------------------------------------------------------
            */

            $variante->nombre = $dto->nombre;
            $variante->activo = $dto->activo;
            $variante->clave_combinacion = $clave;

            $variante->save();

            /*
            |--------------------------------------------------------------------------
            | 7. Sincronizar los valores de atributos
            |--------------------------------------------------------------------------
            */

            $variante->atributosValores()->sync(
                $valores->modelKeys()
            );

            /*
            |--------------------------------------------------------------------------
            | 8. Devolver la variante actualizada
            |--------------------------------------------------------------------------
            */

            return $variante->load('atributosValores.atributo');
        });
    }
}