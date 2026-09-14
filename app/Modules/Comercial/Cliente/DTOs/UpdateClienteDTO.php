<?php

namespace App\Modules\Comercial\Cliente\DTOs;

class UpdateClienteDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nombre,
        public readonly string $email,
        public readonly ?string $telefono,
        public readonly string $ci,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            nombre: (string)$data['nombre'],
            email: (string)$data['email'],
            telefono: $data['telefono'] !== null ? (string)$data['telefono'] : null,
            ci: (string)$data['ci'],
        );
    }

    public function attributes(): array
    {
        return [
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'ci' => $this->ci,
        ];
    }
}