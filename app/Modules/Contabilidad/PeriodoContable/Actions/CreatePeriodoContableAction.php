<?php

namespace App\Modules\Contabilidad\PeriodoContable\Actions;

use Illuminate\Support\Facades\DB;

use App\Models\PeriodoContable;

use App\Modules\Contabilidad\PeriodoContable\DTOs\CreatePeriodoContableDTO;

class CreatePeriodoContableAction
{
    public function __invoke(
        CreatePeriodoContableDTO $dto
    ): PeriodoContable {
        return DB::transaction(
            function () use ($dto) {

                $data = $dto->toArray();

                $localId = $data['local_id'];

                // Buscar período vigente.
                $periodoActual = PeriodoContable::vigenteActual($localId);

                if ($periodoActual) {
                    return PeriodoContable::cerrarYAbrirNuevo(
                        $periodoActual,
                        $data
                    );
                }

                // No hay período vigente.
                // Buscar el último período del local.
                $ultimoPeriodo = PeriodoContable::ultimoDeLocal($localId);

                if ($ultimoPeriodo) {
                    return PeriodoContable::cerrarYAbrirNuevo(
                        $ultimoPeriodo,
                        $data
                    );
                }

                // El local no tiene ningún período.
                return PeriodoContable::create($data);
            }
        );
    }
}