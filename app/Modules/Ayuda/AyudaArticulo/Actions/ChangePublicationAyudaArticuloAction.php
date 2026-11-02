<?php

namespace App\Modules\Ayuda\AyudaArticulo\Actions;

use App\Models\AyudaArticulo;
use App\Modules\Ayuda\AyudaArticulo\DTOs\ChangePublicationAyudaArticuloDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangePublicationAyudaArticuloAction
{
    public function __invoke(
        ChangePublicationAyudaArticuloDTO $dto
    ): AyudaArticulo {

        return DB::transaction(function () use ($dto) {

            $articulo = AyudaArticulo::query()
                ->lockForUpdate()
                ->findOrFail($dto->id);

            /*
            |--------------------------------------------------------------------------
            | Validaciones para publicar
            |--------------------------------------------------------------------------
            */
            if ($dto->publicado) {

                if (
                    trim($articulo->titulo) === ''
                    || trim($articulo->contenido) === ''
                ) {
                    throw ValidationException::withMessages([
                        'publicado' =>
                            'El artículo necesita título y contenido para publicarse.',
                    ]);
                }

                $categoriaActiva = DB::table('ayuda_categorias')
                    ->where('id', $articulo->categoria_id)
                    ->where('activo', true)
                    ->exists();

                if (!$categoriaActiva) {
                    throw ValidationException::withMessages([
                        'categoria_id' =>
                            'No se puede publicar un artículo de una categoría inactiva.',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Actualizar publicación
            |--------------------------------------------------------------------------
            */
            $articulo->update([
                'publicado' => $dto->publicado,
            ]);

            return $articulo->refresh();
        });
    }
}
