<?php

namespace App\Modules\Inventario\ProductoVariante\Services;

use App\Models\AtributableAtributo;
use App\Models\AtributableValor;
use App\Models\AtributoValor;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class ValidarValoresProductoVarianteService
{
    public function validar(
        Producto $producto,
        array $selecciones
    ): Collection {
        $error = fn (string $mensaje) => ValidationException::withMessages([
            'atributosValores' => $mensaje,
        ]);

        if (empty($selecciones)) {
            throw $error('Debe seleccionar al menos un valor de atributo.');
        }

        $ids = [];

        foreach ($selecciones as $seleccion) {
            $id = is_array($seleccion)
                ? ($seleccion['atributo_valor_id'] ?? null)
                : null;

            if (
                ! is_int($id) && ! (
                    is_string($id)
                    && ctype_digit($id)
                )
            ) {
                throw $error('Cada selección debe contener un ID de valor válido.');
            }

            if ((int) $id < 1) {
                throw $error('Cada selección debe contener un ID de valor válido.');
            }

            $ids[] = (int) $id;
        }

        if (count(array_unique($ids)) !== count($ids)) {
            throw $error('No puede seleccionar dos veces el mismo valor.');
        }

        $valores = AtributoValor::query()
            ->whereIn('id', $ids)
            ->with('atributo')
            ->get();

        if ($valores->count() !== count($ids)) {
            throw $error('Uno o más valores de atributo no existen.');
        }

        if ($valores->contains(fn ($valor) => ! $valor->atributo)) {
            throw $error('Uno o más valores no tienen un atributo válido.');
        }

        $atributosIds = $valores->pluck('atributo_id')->all();

        if (count(array_unique($atributosIds)) !== count($atributosIds)) {
            throw $error('Seleccione como máximo un valor por atributo.');
        }

        $valoresAsignados = AtributableValor::query()
            ->where('atributable_type', Producto::class)
            ->where('atributable_id', $producto->id)
            ->where('activo', true)
            ->whereIn('atributo_valor_id', $ids)
            ->pluck('atributo_valor_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (count($valoresAsignados) !== count($ids)) {
            throw $error('Todos los valores deben estar asignados y activos en el producto.');
        }

        $atributosAsignados = AtributableAtributo::query()
            ->where('atributable_type', Producto::class)
            ->where('atributable_id', $producto->id)
            ->where('activo', true)
            ->whereIn('atributo_id', $atributosIds)
            ->pluck('atributo_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->count();

        if ($atributosAsignados !== count(array_unique($atributosIds))) {
            throw $error('Todos los atributos seleccionados deben estar asignados y activos en el producto.');
        }

        return $valores;
    }
}
