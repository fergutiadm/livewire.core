<?php

namespace App\Modules\Usuarios\DTOs;

class ChangePasswordUsuarioDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $password,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) $data['user_id'],
            password: $data['password'],
        );
    }
}