<?php

namespace App\Modules\Ayuda\AyudaCategoria\Actions;

use App\Models\AyudaCategoria;
use App\Modules\Ayuda\AyudaCategoria\DTOs\CreateAyudaCategoriaDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateAyudaCategoriaAction
{
    public function __invoke(
        CreateAyudaCategoriaDTO $dto
    ): AyudaCategoria {

        $data = $dto->toArray();

        $data['nombre'] = trim($data['nombre']);
        $data['slug'] = trim($data['slug']);

        Validator::make($data, [
            'nombre' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('ayuda_categorias', 'slug'),
            ],
            'descripcion' => ['nullable', 'string'],
            'orden_visual' => ['required', 'integer', 'min:0'],
            'activa' => ['required', 'boolean'],
        ])->validate();

        return DB::transaction(
            fn () => AyudaCategoria::create($data)
        );
    }
}
