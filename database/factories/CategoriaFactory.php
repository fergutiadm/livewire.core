<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'local_id' => null, // se define en el seeder
            'nombre' => fake()->words(2, true),
            'descripcion' => fake()->sentence(8),
            'orden_visual' => null, // placeholder
            'porciento_descuento' => fake()->randomFloat(2, 0, 30),
        ];
    }
}

