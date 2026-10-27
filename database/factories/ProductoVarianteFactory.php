<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\ProductoVariante;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoVarianteFactory extends Factory
{
    protected $model = ProductoVariante::class;

    public function definition(): array
    {
        return [
            'producto_id' => Producto::factory(),
            'clave_combinacion' => 'TEST-' .
                strtoupper(fake()->unique()->bothify('????-####')),
            'nombre' => fake()->optional()->words(2, true),
            'activo' => true,
        ];
    }
}
