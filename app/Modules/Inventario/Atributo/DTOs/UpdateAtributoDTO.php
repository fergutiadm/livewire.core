<?php

namespace App\Modules\Inventario\Atributo\DTOs;

class UpdateAtributoDTO
{
    public function __construct(
        public int $id,
        public string $codigo,
        public string $nombre,
        public ?string $descripcion,
        public ?int $orden_visual,
        public ?array $valores = null,
        public ?bool $activo = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],

            codigo: mb_strtoupper(
                trim((string) ($data['codigo'] ?? ''))
            ),

            nombre: trim(
                (string) ($data['nombre'] ?? '')
            ),

            descripcion: isset($data['descripcion'])
                ? trim((string) $data['descripcion'])
                : null,

            orden_visual: isset($data['orden_visual'])
                ? (int) $data['orden_visual']
                : null,

            valores: array_key_exists('valores', $data)
                ? array_map(
                    static fn (array $valor): array => [
                        'id' => isset($valor['id'])
                            ? (int) $valor['id']
                            : null,

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
                )
                : null,

            activo: array_key_exists('activo', $data)
                ? (bool) $data['activo']
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'orden_visual' => $this->orden_visual,
            'valores' => $this->valores,
            'activo' => $this->activo,
        ];
    }
}