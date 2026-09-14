<?php

namespace App\Modules\Inventario\UnidadMedida\Services;

use App\Models\Producto;
use App\Models\ProductoUnidad;
use App\Models\UnidadMedida;
use App\Modules\Inventario\UnidadMedida\Exceptions\ConversionUnidadMedidaException;

class UnidadMedidaConversionService
{
    /**
     * Convierte una cantidad entre dos unidades de medida.
     *
     * Las conversiones pueden ser:
     *
     * 1. Misma unidad.
     * 2. Entre unidades estándar de la misma dimensión.
     * 3. Entre una unidad estándar y una unidad operativa de un producto.
     * 4. Entre dos unidades operativas del mismo producto.
     *
     * Las unidades operativas requieren siempre un producto.
     */
    public function convertir(
        string|int $cantidad,
        UnidadMedida $unidadOrigen,
        UnidadMedida $unidadDestino,
        ?Producto $producto = null,
    ): ConversionUnidadResultado {
        $cantidad = $this->normalizarDecimal($cantidad);

        if ($this->esMenorOIgualACero($cantidad)) {
            throw new ConversionUnidadMedidaException(
                'La cantidad a convertir debe ser mayor que cero.'
            );
        }

        if ($unidadOrigen->id === $unidadDestino->id) {
            return new ConversionUnidadResultado(
                cantidadOriginal: $cantidad,
                unidadOrigen: $unidadOrigen,
                factorAplicado: '1',
                cantidadConvertida: $cantidad,
                unidadDestino: $unidadDestino,
            );
        }

        $origenOperativa = $unidadOrigen->tipo === 'operacional';
        $destinoOperativa = $unidadDestino->tipo === 'operacional';

        if (($origenOperativa || $destinoOperativa) && $producto === null) {
            throw new ConversionUnidadMedidaException(
                'Las conversiones que involucran unidades operativas requieren un producto.'
            );
        }

        if (!$origenOperativa && !$destinoOperativa) {
            return $this->convertirUnidadesEstandar(
                $cantidad,
                $unidadOrigen,
                $unidadDestino
            );
        }

        if ($origenOperativa && $destinoOperativa) {
            return $this->convertirEntreOperativas(
                $cantidad,
                $unidadOrigen,
                $unidadDestino,
                $producto
            );
        }

        if ($origenOperativa) {
            return $this->convertirDesdeOperativa(
                $cantidad,
                $unidadOrigen,
                $unidadDestino,
                $producto
            );
        }

        return $this->convertirHaciaOperativa(
            $cantidad,
            $unidadOrigen,
            $unidadDestino,
            $producto
        );
    }

    /**
     * Conversión entre unidades estándar.
     */
    private function convertirUnidadesEstandar(
        string $cantidad,
        UnidadMedida $unidadOrigen,
        UnidadMedida $unidadDestino,
    ): ConversionUnidadResultado {
        $this->validarUnidadEstandar($unidadOrigen);
        $this->validarUnidadEstandar($unidadDestino);

        if (
            $unidadOrigen->unidad_dimension_id !==
            $unidadDestino->unidad_dimension_id
        ) {
            throw new ConversionUnidadMedidaException(
                "Las unidades {$unidadOrigen->codigo} y {$unidadDestino->codigo} "
                . 'pertenecen a dimensiones incompatibles.'
            );
        }

        $factorOrigen = $this->decimal($unidadOrigen->factor_base);
        $factorDestino = $this->decimal($unidadDestino->factor_base);

        $cantidadBase = bcmul(
            $cantidad,
            $factorOrigen,
            10
        );

        $cantidadConvertida = bcdiv(
            $cantidadBase,
            $factorDestino,
            10
        );

        $factorAplicado = bcdiv(
            $factorOrigen,
            $factorDestino,
            10
        );

        return new ConversionUnidadResultado(
            cantidadOriginal: $cantidad,
            unidadOrigen: $unidadOrigen,
            factorAplicado: $factorAplicado,
            cantidadConvertida: $cantidadConvertida,
            unidadDestino: $unidadDestino,
        );
    }

    /**
     * Convierte desde una unidad operativa hacia una unidad estándar.
     *
     * Ejemplo:
     *
     * Arroz:
     * saco = 50 kg
     *
     * 10 sacos -> 500 kg
     */
    private function convertirDesdeOperativa(
        string $cantidad,
        UnidadMedida $unidadOrigen,
        UnidadMedida $unidadDestino,
        Producto $producto,
    ): ConversionUnidadResultado {
        $productoUnidad = $this->obtenerProductoUnidad(
            $producto,
            $unidadOrigen
        );

        $this->validarUnidadEstandar($unidadDestino);

        $unidadBase = $this->obtenerUnidadBaseDelProducto($producto);

        $cantidadBase = bcmul(
            $cantidad,
            $this->decimal($productoUnidad->factor_a_base),
            10
        );

        $cantidadConvertida = $this->convertirDesdeBase(
            $cantidadBase,
            $unidadBase,
            $unidadDestino
        );

        $factorAplicado = bcdiv(
            $cantidadConvertida,
            $cantidad,
            10
        );

        return new ConversionUnidadResultado(
            cantidadOriginal: $cantidad,
            unidadOrigen: $unidadOrigen,
            factorAplicado: $factorAplicado,
            cantidadConvertida: $cantidadConvertida,
            unidadDestino: $unidadDestino,
        );
    }

    /**
     * Convierte desde una unidad estándar hacia una unidad operativa.
     *
     * Ejemplo:
     *
     * Arroz:
     * saco = 50 kg
     *
     * 500 kg -> 10 sacos
     */
    private function convertirHaciaOperativa(
        string $cantidad,
        UnidadMedida $unidadOrigen,
        UnidadMedida $unidadDestino,
        Producto $producto,
    ): ConversionUnidadResultado {
        $productoUnidad = $this->obtenerProductoUnidad(
            $producto,
            $unidadDestino
        );

        $this->validarUnidadEstandar($unidadOrigen);

        $unidadBase = $this->obtenerUnidadBaseDelProducto($producto);

        $cantidadBase = $this->convertirHaciaBase(
            $cantidad,
            $unidadOrigen,
            $unidadBase
        );

        $cantidadConvertida = bcdiv(
            $cantidadBase,
            $this->decimal($productoUnidad->factor_a_base),
            10
        );

        $factorAplicado = bcdiv(
            $cantidadConvertida,
            $cantidad,
            10
        );

        return new ConversionUnidadResultado(
            cantidadOriginal: $cantidad,
            unidadOrigen: $unidadOrigen,
            factorAplicado: $factorAplicado,
            cantidadConvertida: $cantidadConvertida,
            unidadDestino: $unidadDestino,
        );
    }

    /**
     * Convierte entre dos unidades operativas del mismo producto.
     *
     * Ejemplo:
     *
     * saco = 50 kg
     * paquete = 5 kg
     *
     * 2 sacos -> 20 paquetes
     */
    private function convertirEntreOperativas(
        string $cantidad,
        UnidadMedida $unidadOrigen,
        UnidadMedida $unidadDestino,
        Producto $producto,
    ): ConversionUnidadResultado {
        $productoUnidadOrigen = $this->obtenerProductoUnidad(
            $producto,
            $unidadOrigen
        );

        $productoUnidadDestino = $this->obtenerProductoUnidad(
            $producto,
            $unidadDestino
        );

        $cantidadBase = bcmul(
            $cantidad,
            $this->decimal($productoUnidadOrigen->factor_a_base),
            10
        );

        $cantidadConvertida = bcdiv(
            $cantidadBase,
            $this->decimal($productoUnidadDestino->factor_a_base),
            10
        );

        $factorAplicado = bcdiv(
            $cantidadConvertida,
            $cantidad,
            10
        );

        return new ConversionUnidadResultado(
            cantidadOriginal: $cantidad,
            unidadOrigen: $unidadOrigen,
            factorAplicado: $factorAplicado,
            cantidadConvertida: $cantidadConvertida,
            unidadDestino: $unidadDestino,
        );
    }

    /**
     * Convierte una cantidad desde una unidad estándar hacia
     * la unidad base del producto.
     */
    private function convertirHaciaBase(
        string $cantidad,
        UnidadMedida $unidadOrigen,
        UnidadMedida $unidadBase,
    ): string {
        if (
            $unidadOrigen->unidad_dimension_id !==
            $unidadBase->unidad_dimension_id
        ) {
            throw new ConversionUnidadMedidaException(
                "La unidad {$unidadOrigen->codigo} no es compatible "
                . "con la unidad base {$unidadBase->codigo} del producto."
            );
        }

        return bcmul(
            $cantidad,
            bcdiv(
                $this->decimal($unidadOrigen->factor_base),
                $this->decimal($unidadBase->factor_base),
                10
            ),
            10
        );
    }

    /**
     * Convierte una cantidad expresada en la unidad base del producto
     * hacia otra unidad estándar.
     */
    private function convertirDesdeBase(
        string $cantidadBase,
        UnidadMedida $unidadBase,
        UnidadMedida $unidadDestino,
    ): string {
        if (
            $unidadBase->unidad_dimension_id !==
            $unidadDestino->unidad_dimension_id
        ) {
            throw new ConversionUnidadMedidaException(
                "La unidad {$unidadDestino->codigo} no es compatible "
                . "con la unidad base {$unidadBase->codigo} del producto."
            );
        }

        return bcdiv(
            bcmul(
                $cantidadBase,
                $this->decimal($unidadBase->factor_base),
                10
            ),
            $this->decimal($unidadDestino->factor_base),
            10
        );
    }

    /**
     * Obtiene la configuración de una unidad operativa para un producto.
     */
    private function obtenerProductoUnidad(
        Producto $producto,
        UnidadMedida $unidad,
    ): ProductoUnidad {
        $productoUnidad = ProductoUnidad::query()
            ->where('producto_id', $producto->id)
            ->where('unidad_medida_id', $unidad->id)
            ->where('activo', true)
            ->first();

        if (!$productoUnidad) {
            throw new ConversionUnidadMedidaException(
                "La unidad {$unidad->codigo} no está configurada "
                . "para el producto {$producto->id}."
            );
        }

        $factor = $this->decimal($productoUnidad->factor_a_base);

        if ($this->esMenorOIgualACero($factor)) {
            throw new ConversionUnidadMedidaException(
                "El factor de conversión de la unidad {$unidad->codigo} "
                . 'debe ser mayor que cero.'
            );
        }

        return $productoUnidad;
    }

    /**
     * Obtiene la unidad base configurada para el producto.
     */
    private function obtenerUnidadBaseDelProducto(
        Producto $producto,
    ): UnidadMedida {
        $unidadBase = $producto->unidadMedidaBase;

        if (!$unidadBase) {
            throw new ConversionUnidadMedidaException(
                "El producto {$producto->id} no tiene una unidad de medida base configurada."
            );
        }

        $this->validarUnidadEstandar($unidadBase);

        return $unidadBase;
    }

    /**
     * Valida que una unidad estándar tenga una configuración coherente.
     */
    private function validarUnidadEstandar(
        UnidadMedida $unidad,
    ): void {
        if ($unidad->tipo !== 'estandar') {
            throw new ConversionUnidadMedidaException(
                "La unidad {$unidad->codigo} no es una unidad estándar."
            );
        }

        if (!$unidad->unidad_dimension_id) {
            throw new ConversionUnidadMedidaException(
                "La unidad estándar {$unidad->codigo} debe tener una dimensión."
            );
        }

        if ($unidad->factor_base === null) {
            throw new ConversionUnidadMedidaException(
                "La unidad estándar {$unidad->codigo} debe tener un factor base."
            );
        }

        if (
            $this->esMenorOIgualACero(
                $this->decimal($unidad->factor_base)
            )
        ) {
            throw new ConversionUnidadMedidaException(
                "El factor base de la unidad {$unidad->codigo} debe ser mayor que cero."
            );
        }
    }

    /**
     * Normaliza una cantidad decimal recibida como string o entero.
     */
    private function normalizarDecimal(string|int $valor): string
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            throw new ConversionUnidadMedidaException(
                'La cantidad no puede estar vacía.'
            );
        }

        if (!preg_match('/^\d+(?:\.\d+)?$/', $valor)) {
            throw new ConversionUnidadMedidaException(
                'La cantidad debe ser un número decimal positivo válido.'
            );
        }

        return $valor;
    }

    /**
     * Convierte cualquier valor decimal recibido por Eloquent
     * en una cadena apta para BCMath.
     */
    private function decimal(mixed $valor): string
    {
        if ($valor === null) {
            throw new ConversionUnidadMedidaException(
                'Se esperaba un valor decimal y se recibió null.'
            );
        }

        return (string) $valor;
    }

    private function esMenorOIgualACero(string $valor): bool
    {
        return bccomp($valor, '0', 10) <= 0;
    }
}