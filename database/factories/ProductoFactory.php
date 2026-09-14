<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Local;
use App\Models\Moneda;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $precio = fake()->randomFloat(2, 10, 500);

        return [
            // global o por local
            'local_id' => fake()->boolean(70)
                ? Local::inRandomOrder()->value('id')
                : null,

            'categoria_id' => Categoria::inRandomOrder()->value('id'),
            'moneda_id' => Moneda::inRandomOrder()->value('id'),

            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(10),
            'orden_visual' => fake()->numberBetween(1, 50),
            'porciento_descuento' => fake()->randomFloat(2, 0, 25),

            'costo' => $precio * 0.6,
            'precio' => $precio,
            'codigo' => fake()->unique()->bothify('PRD-####'),
        ];
    }
}
