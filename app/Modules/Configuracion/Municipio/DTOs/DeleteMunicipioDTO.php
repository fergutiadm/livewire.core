<?php

namespace App\Modules\Configuracion\Municipio\DTOs;

class DeleteMunicipioDTO
{
    public function __construct(
        public readonly int $id
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
        );
    }
}