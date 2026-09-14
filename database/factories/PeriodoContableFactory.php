<?php

namespace Database\Factories;

use App\Models\PeriodoContable;
use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PeriodoContableFactory extends Factory
{
    protected $model = PeriodoContable::class;

    public function definition(): array
    {
        // Elegimos un local aleatorio, si no hay locales se crea uno
        $localId = Local::inRandomOrder()->value('id') ?? Local::factory()->create()->id;

        // Por defecto generamos un periodo de 7 días, inicio lunes
        $fechaInicio = Carbon::now()->startOfWeek(); // lunes de la semana actual
        $fechaFin = (clone $fechaInicio)->addDays(6);

        return [
            'local_id' => $localId,
            'nombre' => 'Periodo ' . $fechaInicio->format('d/m/Y'),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'activo' => true,   // por default activo
            'cerrado' => false, // por default no cerrado
        ];
    }
}

