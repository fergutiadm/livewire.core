<?php

namespace App\Modules\Ayuda\AyudaCategoria\Actions;

use App\Models\AyudaCategoria;
use App\Modules\Ayuda\AyudaCategoria\DTOs\UpdateAyudaCategoriaDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateAyudaCategoriaAction
{
    public function __invoke(
        UpdateAyudaCategoriaDTO $dto
    ): AyudaCategoria {

        return DB::transaction(function () use ($dto) {

            $model = AyudaCategoria::findOrFail($dto->id);

            $data = $dto->attributes();

            $data['nombre'] = trim($data['nombre']);
            $data['slug'] = trim($data['slug']);

            Validator::make($data, [
                'nombre' => ['required', 'string', 'max:150'],
                'slug' => [
                    'required',
                    'string',
                    'max:160',
                    'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                    Rule::unique('ayuda_categorias', 'slug')
                        ->ignore($model->id),
                ],
                'descripcion' => ['nullable', 'string'],
                'orden_visual' => ['required', 'integer', 'min:0'],
                'activa' => ['required', 'boolean'],
            ])->validate();

            $model->update($data);

            return $model->refresh();
        });
    }
}
