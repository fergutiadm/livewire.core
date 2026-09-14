<?php

namespace Database\Seeders;

use App\Models\Moneda;
use Illuminate\Database\Seeder;

class MonedaSeeder extends Seeder
{
    public function run(): void
    {
        // Moneda base / principal
        Moneda::updateOrCreate(
            ['codigo' => 'CUP'],
            [
                'nombre'       => 'Peso Cubano',
                'simbolo'      => '$',
                'es_principal' => true,
                'tasa_cambio'  => 1,
                'activa'       => true,
                'color_bg'     => 'bg-blue-500',
                'color_text'   => 'text-white',
            ]
        );

        // USD
        Moneda::updateOrCreate(
            ['codigo' => 'USD'],
            [
                'nombre'       => 'Dólar Estadounidense',
                'simbolo'      => '$',
                'es_principal' => false,
                'tasa_cambio'  => 450, // ejemplo
                'activa'       => true,
                'color_bg'     => 'bg-green-500',
                'color_text'   => 'text-white',
            ]
        );

        // EUR
        Moneda::updateOrCreate(
            ['codigo' => 'EUR'],
            [
                'nombre'       => 'Euro',
                'simbolo'      => '€',
                'es_principal' => false,
                'tasa_cambio'  => 480, // ejemplo
                'activa'       => true,
                'color_bg'     => 'bg-purple-500',
                'color_text'   => 'text-white',
            ]
        );

        // MLC
        Moneda::updateOrCreate(
            ['codigo' => 'MLC'],
            [
                'nombre'       => 'Euro',
                'simbolo'      => '$',
                'es_principal' => false,
                'tasa_cambio'  => 400, // ejemplo
                'activa'       => true,
                'color_bg'     => 'bg-yellow-500',
                'color_text'   => 'text-black',
            ]
        );
    }
}
