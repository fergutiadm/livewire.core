<?php

namespace Database\Factories;

use App\Models\Atributo;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtributoFactory extends Factory
{
    protected $model = Atributo::class;

    public function definition(): array
    {
        return [
            'codigo' => strtoupper(fake()->unique()->bothify('ATR-####')),
            'nombre' => fake()->word(),
            'descripcion' => fake()->optional()->sentence(),
            'orden_visual' => fake()->numberBetween(1, 50),
            'activo' => true,
        ];
    }
}
