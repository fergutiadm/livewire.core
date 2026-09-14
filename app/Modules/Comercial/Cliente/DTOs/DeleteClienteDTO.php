<?php

namespace App\Modules\Comercial\Cliente\DTOs;

class DeleteClienteDTO
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