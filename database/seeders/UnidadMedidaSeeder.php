<?php

namespace Database\Seeders;

use App\Models\UnidadDimension;
use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $dimensiones = [
            [
                'codigo' => 'masa',
                'nombre' => 'Masa',
                'descripcion' => 'Unidades utilizadas para medir masa.',
            ],
            [
                'codigo' => 'volumen',
                'nombre' => 'Volumen',
                'descripcion' => 'Unidades utilizadas para medir volumen.',
            ],
            [
                'codigo' => 'longitud',
                'nombre' => 'Longitud',
                'descripcion' => 'Unidades utilizadas para medir longitud.',
            ],
            [
                'codigo' => 'area',
                'nombre' => 'Área',
                'descripcion' => 'Unidades utilizadas para medir superficie.',
            ],
            [
                'codigo' => 'cantidad',
                'nombre' => 'Cantidad',
                'descripcion' => 'Unidades utilizadas para contar elementos.',
            ],
        ];

        foreach ($dimensiones as $dimension) {
            UnidadDimension::updateOrCreate(
                ['codigo' => $dimension['codigo']],
                $dimension + ['activo' => true]
            );
        }

        $masa = UnidadDimension::where('codigo', 'masa')->firstOrFail();
        $volumen = UnidadDimension::where('codigo', 'volumen')->firstOrFail();
        $longitud = UnidadDimension::where('codigo', 'longitud')->firstOrFail();
        $area = UnidadDimension::where('codigo', 'area')->firstOrFail();
        $cantidad = UnidadDimension::where('codigo', 'cantidad')->firstOrFail();

        $unidades = [
            [
                'codigo' => 'kg',
                'nombre' => 'Kilogramo',
                'abreviatura' => 'kg',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $masa->id,
                'factor_base' => 1,
                'es_base_dimension' => true,
            ],
            [
                'codigo' => 'g',
                'nombre' => 'Gramo',
                'abreviatura' => 'g',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $masa->id,
                'factor_base' => 0.001,
                'es_base_dimension' => false,
            ],
            [
                'codigo' => 'mg',
                'nombre' => 'Miligramo',
                'abreviatura' => 'mg',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $masa->id,
                'factor_base' => 0.000001,
                'es_base_dimension' => false,
            ],

            [
                'codigo' => 'l',
                'nombre' => 'Litro',
                'abreviatura' => 'L',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $volumen->id,
                'factor_base' => 1,
                'es_base_dimension' => true,
            ],
            [
                'codigo' => 'ml',
                'nombre' => 'Mililitro',
                'abreviatura' => 'ml',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $volumen->id,
                'factor_base' => 0.001,
                'es_base_dimension' => false,
            ],

            [
                'codigo' => 'm',
                'nombre' => 'Metro',
                'abreviatura' => 'm',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $longitud->id,
                'factor_base' => 1,
                'es_base_dimension' => true,
            ],
            [
                'codigo' => 'cm',
                'nombre' => 'Centímetro',
                'abreviatura' => 'cm',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $longitud->id,
                'factor_base' => 0.01,
                'es_base_dimension' => false,
            ],
            [
                'codigo' => 'mm',
                'nombre' => 'Milímetro',
                'abreviatura' => 'mm',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $longitud->id,
                'factor_base' => 0.001,
                'es_base_dimension' => false,
            ],

            [
                'codigo' => 'm2',
                'nombre' => 'Metro cuadrado',
                'abreviatura' => 'm²',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $area->id,
                'factor_base' => 1,
                'es_base_dimension' => true,
            ],
            [
                'codigo' => 'cm2',
                'nombre' => 'Centímetro cuadrado',
                'abreviatura' => 'cm²',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $area->id,
                'factor_base' => 0.0001,
                'es_base_dimension' => false,
            ],

            [
                'codigo' => 'unidad',
                'nombre' => 'Unidad',
                'abreviatura' => 'un',
                'tipo' => 'estandar',
                'unidad_dimension_id' => $cantidad->id,
                'factor_base' => 1,
                'es_base_dimension' => true,
            ],

            [
                'codigo' => 'saco',
                'nombre' => 'Saco',
                'abreviatura' => 'saco',
                'tipo' => 'operacional',
                'unidad_dimension_id' => null,
                'factor_base' => null,
                'es_base_dimension' => false,
            ],
            [
                'codigo' => 'caja',
                'nombre' => 'Caja',
                'abreviatura' => 'caja',
                'tipo' => 'operacional',
                'unidad_dimension_id' => null,
                'factor_base' => null,
                'es_base_dimension' => false,
            ],
            [
                'codigo' => 'rollo',
                'nombre' => 'Rollo',
                'abreviatura' => 'rollo',
                'tipo' => 'operacional',
                'unidad_dimension_id' => null,
                'factor_base' => null,
                'es_base_dimension' => false,
            ],
            [
                'codigo' => 'paquete',
                'nombre' => 'Paquete',
                'abreviatura' => 'paq',
                'tipo' => 'operacional',
                'unidad_dimension_id' => null,
                'factor_base' => null,
                'es_base_dimension' => false,
            ],
        ];

        foreach ($unidades as $unidad) {
            UnidadMedida::updateOrCreate(
                ['codigo' => $unidad['codigo']],
                $unidad + ['activo' => true]
            );
        }
    }
}