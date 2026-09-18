<?php

namespace App\Modules\Inventario\Atributo\DTOs;

class CreateAtributoDTO
{
    public function __construct(
        public string $codigo,
        public string $nombre,
        public ?string $descripcion,
        public ?int $orden_visual,
        public array $valores = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            codigo: mb_strtoupper(trim((string) ($data['codigo'] ?? ''))),
            nombre: trim((string) ($data['nombre'] ?? '')),
            descripcion: isset($data['descripcion'])
                ? trim((string) $data['descripcion'])
                : null,
            orden_visual: isset($data['orden_visual'])
                ? (int) $data['orden_visual']
                : null,
            valores: array_map(
                static fn (array $valor): array => [
                    'codigo' => mb_strtoupper(
                        trim((string) ($valor['codigo'] ?? ''))
                    ),
                    'nombre' => trim(
                        (string) ($valor['nombre'] ?? '')
                    ),
                    'orden_visual' => isset($valor['orden_visual'])
                        ? (int) $valor['orden_visual']
                        : null,
                ],
                $data['valores'] ?? []
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'orden_visual' => $this->orden_visual,
            'valores' => $this->valores,
        ];
    }
}
