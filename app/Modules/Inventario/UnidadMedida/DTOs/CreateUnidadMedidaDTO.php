<?php

namespace App\Modules\Inventario\UnidadMedida\DTOs;

class CreateUnidadMedidaDTO
{
    public function __construct(
        public readonly ?int $unidad_dimension_id,
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly ?string $abreviatura,
        public readonly string $tipo,
        public readonly ?float $factor_base,
        public readonly bool $es_base_dimension,
        public readonly bool $activo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            unidad_dimension_id: $data['unidad_dimension_id'] !== null ? (int)$data['unidad_dimension_id'] : null,
            codigo: (string)$data['codigo'],
            nombre: (string)$data['nombre'],
            abreviatura: $data['abreviatura'] !== null ? (string)$data['abreviatura'] : null,
            tipo: (string)$data['tipo'],
            factor_base: $data['factor_base'] !== null ? (float)$data['factor_base'] : null,
            es_base_dimension: (bool)$data['es_base_dimension'],
            activo: (bool)$data['activo'],
        );
    }

    public function toArray(): array
    {
        return [
            'unidad_dimension_id' => $this->unidad_dimension_id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'abreviatura' => $this->abreviatura,
            'tipo' => $this->tipo,
            'factor_base' => $this->factor_base,
            'es_base_dimension' => $this->es_base_dimension,
            'activo' => $this->activo,
        ];
    }
}