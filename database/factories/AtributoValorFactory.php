<?php

namespace Database\Factories;

use App\Models\Atributo;
use App\Models\AtributoValor;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtributoValorFactory extends Factory
{
    protected $model = AtributoValor::class;

    public function definition(): array
    {
        return [
            'atributo_id' => Atributo::factory(),
            'codigo' => strtoupper(fake()->unique()->bothify('VAL-####')),
            'nombre' => fake()->word(),
            'orden_visual' => fake()->numberBetween(1, 50),
            'activo' => true,
        ];
    }
}
