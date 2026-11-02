<?php

namespace App\Modules\Ayuda\AyudaArticulo\DTOs;

use Illuminate\Support\Facades\Validator;

class ChangePublicationAyudaArticuloDTO
{
    public function __construct(
        public readonly int $id,
        public readonly bool $publicado,
    ) {}

    public static function fromArray(array $data): self
    {
        $data = Validator::make($data, [
            'id' => ['required', 'integer', 'min:1'],
            'publicado' => ['required', 'boolean'],
        ])->validate();

        return new self(
            id: (int) $data['id'],
            publicado: (bool) $data['publicado'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'publicado' => $this->publicado,
        ];
    }
}
