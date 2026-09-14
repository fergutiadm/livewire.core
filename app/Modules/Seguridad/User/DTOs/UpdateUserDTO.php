<?php

namespace App\Modules\Seguridad\User\DTOs;

class UpdateUserDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password,
        public readonly ?string $movil,
        public readonly string $role,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            name: (string)$data['name'],
            email: (string)$data['email'],
            password: $data['password'] !== null ? (string)$data['password'] : null,
            movil: $data['movil'] !== null ? (string)$data['movil'] : null,
            role: (string) $data['role'],
        );
    }

    public function attributes(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'movil' => $this->movil,
        ];
    }
}