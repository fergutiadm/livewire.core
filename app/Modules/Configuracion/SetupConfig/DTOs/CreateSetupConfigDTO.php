<?php

namespace App\Modules\Configuracion\SetupConfig\DTOs;

class CreateSetupConfigDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $item_id,
        public readonly ?string $descripcion,
        public readonly int $tipo,
        public readonly string $valor,
        public readonly ?string $valor_inicial,
        public readonly ?string $valor_minimo,
        public readonly ?string $valor_maximo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: (string) $data['nombre'],
            item_id: trim((string) $data['item_id']),
            descripcion: self::nullableString($data['descripcion'] ?? null),
            tipo: (int) $data['tipo'],
            valor: (string) $data['valor'],
            valor_inicial: self::nullableString($data['valor_inicial'] ?? null),
            valor_minimo: self::nullableString($data['valor_minimo'] ?? null),
            valor_maximo: self::nullableString($data['valor_maximo'] ?? null),
        );
    }

    private static function nullableString(mixed $value): ?string
    {
        return $value === null || $value === ''
            ? null
            : (string) $value;
    }

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'item_id' => $this->item_id,
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'valor' => $this->valor,
            'valor_inicial' => $this->valor_inicial,
            'valor_minimo' => $this->valor_minimo,
            'valor_maximo' => $this->valor_maximo,
        ];
    }
}
