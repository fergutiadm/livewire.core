<?php

namespace App\Modules\Comercial\Gestor\Actions;

use App\Models\Gestor;
use App\Models\User;
use App\Modules\Comercial\Gestor\DTOs\CreateGestorDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class CreateGestorAction
{
    public function __invoke(CreateGestorDTO $dto): Gestor
    {
        return DB::transaction(function () use ($dto) {

            $user = User::findOrFail($dto->user_id);

            if (!$user->hasRole('gestor')) {
                throw new RuntimeException(
                    'El usuario seleccionado no tiene el rol de gestor.'
                );
            }

            $gestorExiste = Gestor::withTrashed()
                ->where('user_id', $dto->user_id)
                ->exists();

            if ($gestorExiste) {
                throw new RuntimeException(
                    'El usuario seleccionado ya está asociado a un gestor.'
                );
            }

            $slugBase = Str::slug($dto->nombre_comercial);

            if ($slugBase === '') {
                throw new RuntimeException(
                    'No se pudo generar un slug válido a partir del nombre comercial.'
                );
            }

            $slug = $slugBase;
            $contador = 2;

            while (
                Gestor::withTrashed()
                    ->where('slug', $slug)
                    ->exists()
            ) {
                $slug = $slugBase . '-' . $contador;
                $contador++;
            }

            return Gestor::create([
                'user_id' => $dto->user_id,
                'slug' => $slug,
                'nombre_comercial' => $dto->nombre_comercial,
                'descripcion' => $dto->descripcion,
                'activo' => $dto->activo,
            ]);
        });
    }
}