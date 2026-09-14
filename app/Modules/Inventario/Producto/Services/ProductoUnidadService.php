<?php

namespace App\Modules\Inventario\Producto\Services;

use App\Models\Producto;
use App\Models\ProductoUnidad;
use App\Models\UnidadMedida;
use App\Models\MovimientoInventarioDetalle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class ProductoUnidadService
{
    /**
     * Crea una unidad operacional para un producto.
     */
    public function crear(
        Producto $producto,
        UnidadMedida $unidad,
        string $factorABase,
        bool $permiteFraccion = false
    ): ProductoUnidad {
        return DB::transaction(function () use (
            $producto,
            $unidad,
            $factorABase,
            $permiteFraccion
        ) {
            $this->validarProducto($producto);
            $this->validarUnidadOperacional($unidad);
            $this->validarFactor($factorABase);

            $existe = ProductoUnidad::query()
                ->where('producto_id', $producto->id)
                ->where('unidad_medida_id', $unidad->id)
                ->exists();

            if ($existe) {
                throw new RuntimeException(
                    'El producto ya tiene configurada esta unidad de medida.'
                );
            }

            return ProductoUnidad::create([
                'producto_id' => $producto->id,
                'unidad_medida_id' => $unidad->id,
                'factor_a_base' => $factorABase,
                'permite_fraccion' => $permiteFraccion,
                'activo' => true,
            ]);
        });
    }

    /**
     * Actualiza la configuración de una unidad operacional.
     *
     * El producto y la unidad no forman parte de la actualización:
     * esa identidad queda fija.
     */
    public function actualizar(
        ProductoUnidad $productoUnidad,
        string $factorABase,
        bool $permiteFraccion
    ): ProductoUnidad {
        return DB::transaction(function () use (
            $productoUnidad,
            $factorABase,
            $permiteFraccion
        ) {
            $productoUnidad = ProductoUnidad::query()
                ->whereKey($productoUnidad->id)
                ->lockForUpdate()
                ->first();

            if (!$productoUnidad) {
                throw (new ModelNotFoundException)
                    ->setModel(ProductoUnidad::class, [$productoUnidad]);
            }

            $this->validarFactor($factorABase);

            if ($this->fueUtilizadaHistoricamente($productoUnidad)) {
                throw new RuntimeException(
                    'No se puede modificar el factor ni la fraccionabilidad '
                    . 'porque esta configuración ya fue utilizada en un movimiento.'
                );
            }

            $productoUnidad->update([
                'factor_a_base' => $factorABase,
                'permite_fraccion' => $permiteFraccion,
            ]);

            return $productoUnidad->refresh();
        });
    }

    /**
     * Activa una configuración.
     */
    public function activar(ProductoUnidad $productoUnidad): ProductoUnidad
    {
        $productoUnidad->loadMissing('unidad');

        $this->validarUnidadOperacional($productoUnidad->unidad);

        $productoUnidad->update([
            'activo' => true,
        ]);

        return $productoUnidad->refresh();
    }

    /**
     * Desactiva una configuración.
     *
     * El historial no impide desactivarla.
     */
    public function desactivar(ProductoUnidad $productoUnidad): ProductoUnidad
    {
        $productoUnidad->update([
            'activo' => false,
        ]);

        return $productoUnidad->refresh();
    }

    /**
     * Busca una configuración, activa o inactiva.
     */
    public function buscarConfiguracion(
        Producto $producto,
        UnidadMedida $unidad
    ): ?ProductoUnidad {
        return ProductoUnidad::query()
            ->where('producto_id', $producto->id)
            ->where('unidad_medida_id', $unidad->id)
            ->first();
    }

    /**
     * Resuelve una configuración utilizable para una operación.
     *
     * Solo devuelve configuraciones activas.
     */
    public function resolverParaOperacion(
        Producto $producto,
        UnidadMedida $unidad
    ): ProductoUnidad {
        $configuracion = ProductoUnidad::query()
            ->with('unidad')
            ->where('producto_id', $producto->id)
            ->where('unidad_medida_id', $unidad->id)
            ->where('activo', true)
            ->first();

        if (!$configuracion) {
            throw new RuntimeException(
                'El producto no tiene una configuración activa para la unidad indicada.'
            );
        }

        $this->validarUnidadOperacional($configuracion->unidad);

        return $configuracion;
    }

    /**
     * Determina si la relación producto + unidad ya apareció
     * en algún detalle histórico de movimiento.
     */
    private function fueUtilizadaHistoricamente(
        ProductoUnidad $productoUnidad
    ): bool {
        return MovimientoInventarioDetalle::query()
            ->where('producto_id', $productoUnidad->producto_id)
            ->where(
                'unidad_medida_original_id',
                $productoUnidad->unidad_medida_id
            )
            ->exists();
    }

    private function validarProducto(Producto $producto): void
    {
        if (!$producto->unidad_medida_base_id) {
            throw new InvalidArgumentException(
                'El producto debe tener definida una unidad de medida base.'
            );
        }

        $producto->loadMissing('unidadMedidaBase');

        if (!$producto->unidadMedidaBase) {
            throw new InvalidArgumentException(
                'La unidad de medida base del producto no existe.'
            );
        }

        if ($producto->unidadMedidaBase->tipo !== 'estandar') {
            throw new InvalidArgumentException(
                'La unidad base del producto debe ser una unidad estándar.'
            );
        }
    }

    private function validarUnidadOperacional(UnidadMedida $unidad): void
    {
        if ($unidad->tipo !== 'operacional') {
            throw new InvalidArgumentException(
                'ProductoUnidad solo puede utilizar unidades operacionales.'
            );
        }

        if (!$unidad->activo) {
            throw new InvalidArgumentException(
                'La unidad de medida no está activa.'
            );
        }
    }

    private function validarFactor(string $factor): void
    {
        if (!preg_match('/^\d+(?:\.\d+)?$/', $factor)) {
            throw new InvalidArgumentException(
                'El factor debe ser un número decimal positivo.'
            );
        }

        if (bccomp($factor, '0', 10) <= 0) {
            throw new InvalidArgumentException(
                'El factor debe ser mayor que cero.'
            );
        }
    }
}