<?php

namespace App\Http\Livewire\Currency\Actions;

use App\Models\Moneda;
use App\Http\Livewire\Currency\DTOs\SaveTasasCambioDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaveTasasCambioAction
{
    public function execute(SaveTasasCambioDTO $dto): void
        {
            DB::transaction(function () use ($dto): void {

                foreach ($dto->monedas as $moneda) {

                    $tasa = filter_var(
                        $moneda['tasa_cambio'] ?? null,
                        FILTER_VALIDATE_INT
                    );

                    if ($tasa === false || $tasa < 0) {
                        throw ValidationException::withMessages([
                            'monedas' => 'Hay tasas de cambio inválidas.',
                        ]);
                    }

                    $registro = Moneda::query()
                        ->where('activa', true)
                        ->where('es_principal', false)
                        ->lockForUpdate()
                        ->findOrFail($moneda['id']);

                    $registro->tasa_cambio = $tasa;

                    if ($registro->isDirty('tasa_cambio')) {
                        $registro->save();
                    }
                }
            });
        }
}
