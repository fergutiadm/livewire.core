<?php

namespace App\Modules\Finanzas\ConceptoMonedaFondo\DTOs;

class DeleteConceptoMonedaFondoDTO
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