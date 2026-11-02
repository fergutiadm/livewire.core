<?php

namespace App\Modules\Ayuda\AyudaArticulo\Actions;

use App\Models\AyudaArticulo;
use App\Modules\Ayuda\AyudaArticulo\DTOs\CreateAyudaArticuloDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateAyudaArticuloAction
{
    public function __invoke(
        CreateAyudaArticuloDTO $dto
    ): AyudaArticulo {

        $data = $dto->toArray();

        $data['titulo'] = trim($data['titulo']);
        $data['slug'] = trim($data['slug']);
        $data['publicado'] = false;

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
                Rule::unique('ayuda_articulos', 'slug'),
            ],
            'resumen' => ['nullable', 'string'],
            'contenido' => ['present', 'string'],
            'contexto' => ['nullable', 'string', 'max:160'],
            'orden_visual' => ['required', 'integer', 'min:0'],
            'publicado' => ['required', 'boolean'],
        ])->validate();

        return DB::transaction(
            fn () => AyudaArticulo::create($data)
        );
    }
}
