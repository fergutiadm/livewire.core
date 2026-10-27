<?php

namespace App\Modules\Inventario\ProductoVariante\DTOs;

class CreateProductoVarianteDTO
{
    public function __construct(
        public int $productoId,
        public array $atributosValores,
        public ?string $nombre = null,
        public bool $activo = true,
        public ?int $productoVarianteId = null,
    ) {}

    /**
     * Construye el DTO a partir de los datos validados.
     *
     * @param array{
     *     producto_id: int,
     *     atributos_valores: array<int, array{atributo_valor_id: int}>,
     *     nombre?: string|null,
     *     activo?: bool,
     *     producto_variante_id?: int|null
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            productoId: (int) $data['producto_id'],
            atributosValores: $data['atributos_valores'],
            nombre: $data['nombre'] ?? null,
            activo: (bool) ($data['activo'] ?? true),
            productoVarianteId: isset($data['producto_variante_id'])
                ? (int) $data['producto_variante_id']
                : null,
        );
    }

    public function toArray(): array
    {
        return [
            'producto_id' => $this->productoId,
            'atributos_valores' => $this->atributosValores,
            'nombre' => $this->nombre,
            'activo' => $this->activo,
            'producto_variante_id' => $this->productoVarianteId,
        ];
    }
}