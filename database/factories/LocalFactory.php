<?php

namespace Database\Factories;

use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocalFactory extends Factory
{
    protected $model = Local::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'descripcion' => fake()->sentence(6),
        ];
    }
}
