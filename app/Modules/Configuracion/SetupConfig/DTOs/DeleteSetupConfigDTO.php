<?php

namespace App\Modules\Configuracion\SetupConfig\DTOs;

class DeleteSetupConfigDTO
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
}
