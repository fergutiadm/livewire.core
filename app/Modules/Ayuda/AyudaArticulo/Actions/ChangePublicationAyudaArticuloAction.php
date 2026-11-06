<?php

namespace App\Modules\Ayuda\AyudaArticulo\Actions;

use App\Models\AyudaArticulo;
use App\Modules\Ayuda\AyudaArticulo\DTOs\ChangePublicationAyudaArticuloDTO;
use App\Modules\Ayuda\Services\AyudaContenidoValidator;
use App\Modules\Ayuda\Services\AyudaHtmlSanitizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangePublicationAyudaArticuloAction
{

    public function __construct(
        private AyudaHtmlSanitizer $sanitizer,
        private AyudaContenidoValidator $contenidoValidator
    ) {}

    public function __invoke(
        ChangePublicationAyudaArticuloDTO $dto
    ): AyudaArticulo {

        return DB::transaction(function () use ($dto) {

            $articulo = AyudaArticulo::query()
                ->lockForUpdate()
                ->findOrFail($dto->id);

            if ($dto->publicado) {
                $contenido = $this->sanitizer->sanitize(
                    $articulo->contenido
                );

                $this->contenidoValidator->validate(
                    $contenido,
                    requerido: true
                );

                $articulo->contenido = $contenido;
            }

            /*
            |--------------------------------------------------------------------------
            | Validaciones para publicar
            |--------------------------------------------------------------------------
            */
            if ($dto->publicado) {

                if (trim($articulo->titulo) === '') {
                    throw ValidationException::withMessages([
                        'titulo' => 'El artículo necesita un título para publicarse.',
                    ]);
                }

                $categoriaActiva = DB::table('ayuda_categorias')
                    ->where('id', $articulo->categoria_id)
                    ->where('activa', true)
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
