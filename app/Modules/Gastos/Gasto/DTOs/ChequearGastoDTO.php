<?php

namespace App\Modules\Gastos\Gasto\DTOs;

class ChequearGastoDTO
{
    public function __construct(
        public readonly int $id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
        ];
    }
}