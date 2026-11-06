<?php

namespace App\Modules\Ayuda\AyudaArticulo\Actions;

use App\Models\AyudaArticulo;
use App\Modules\Ayuda\AyudaArticulo\DTOs\UpdateAyudaArticuloDTO;
use App\Modules\Ayuda\Services\AyudaContenidoValidator;
use App\Modules\Ayuda\Services\AyudaHtmlSanitizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateAyudaArticuloAction
{
    public function __construct(
        private AyudaHtmlSanitizer $sanitizer,
        private AyudaContenidoValidator $contenidoValidator
    ) {}

    public function __invoke(
        UpdateAyudaArticuloDTO $dto
    ): AyudaArticulo {

        return DB::transaction(function () use ($dto) {

            $model = AyudaArticulo::query()
                ->lockForUpdate()
                ->findOrFail($dto->id);

            $data = $dto->attributes();

            $data['titulo'] = trim($data['titulo']);
            $data['slug'] = trim($data['slug']);

            $data['contenido'] = $this->sanitizer
                ->sanitize($data['contenido'] ?? '');

            $this->contenidoValidator->validate(
                $data['contenido'],
                requerido: $model->publicado
            );

            Validator::make($data, [
                'categoria_id' => [
                    'required',
                    'integer',
                    Rule::exists('ayuda_categorias', 'id'),
                ],
                'titulo' => ['required', 'string', 'max:200'],
                'slug' => [
                    'required',
                    'string',
                    'max:220',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('ayuda_articulos', 'slug')
                        ->ignore($model->id),
                ],
                'resumen' => ['nullable', 'string'],
                'contenido' => ['present', 'string'],
                'contexto' => ['nullable', 'string', 'max:160'],
                // 'orden_visual' => ['nullable', 'required', 'integer', 'min:0'],
            ])->validate();

            if ($model->publicado) {

                $categoriaActiva = DB::table('ayuda_categorias')
                    ->where('id', $data['categoria_id'])
                    ->where('activa', true)
                    ->exists();

                if (!$categoriaActiva) {
                    throw ValidationException::withMessages([
                        'categoria_id' =>
                            'Un artículo publicado requiere una categoría activa.',
                    ]);
                }
            }

            $model->update($data);

            return $model->refresh();
        });
    }
}
