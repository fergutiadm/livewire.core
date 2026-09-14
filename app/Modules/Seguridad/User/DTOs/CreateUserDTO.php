<?php

namespace App\Modules\Seguridad\User\DTOs;

class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $movil,
        public readonly string $role,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string)$data['name'],
            email: (string)$data['email'],
            password: (string)$data['password'],
            movil: $data['movil'] !== null ? (string)$data['movil'] : null,
            role: (string) $data['role'],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'movil' => $this->movil,
        ];
    }
}