<?php

namespace App\Modules\Inventario\ProductoVariante\Services;

use App\Models\AtributoValor;
use App\Models\Producto;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class GeneradorClaveCombinacion
{
    public function generar(Producto $producto, Collection $valores): string
    {
        if ($valores->isEmpty()) {
            throw new InvalidArgumentException(
                'La combinación debe contener al menos un valor.'
            );
        }

        $partes = $valores
            ->map(function (AtributoValor $valor): array {
                $atributo = $valor->atributo;

                if (!$atributo) {
                    throw new InvalidArgumentException(
                        'Un valor no tiene un atributo asociado.'
                    );
                }

                $codigoAtributo = strtoupper(trim($atributo->codigo));
                $codigoValor = strtoupper(trim($valor->codigo));

                if ($codigoAtributo === '' || $codigoValor === '') {
                    throw new InvalidArgumentException(
                        'Los códigos no pueden estar vacíos.'
                    );
                }

                return [
                    'atributo' => $codigoAtributo,
                    'valor' => $codigoValor,
                ];
            })
            ->sortBy('atributo')
            ->values();

        if ($partes->pluck('atributo')->unique()->count() !== $partes->count()) {
            throw new InvalidArgumentException(
                'No se puede repetir un atributo en la combinación.'
            );
        }

        return $partes
            ->map(fn (array $parte) =>
                $parte['atributo'].'='.$parte['valor']
            )
            ->implode('|');
    }
}