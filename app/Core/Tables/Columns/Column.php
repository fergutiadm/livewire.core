<?php

namespace App\Core\Tables\Columns;

abstract class Column
{
    protected ?\Closure $formatStateUsing = null;

    protected string $alignment = 'left';

    public function __construct(
        public string $label,
        public string $field,
        public ?bool $extra = false,
        public ?string $extra_msj = 'Clic para cambiar'
    ) {}

    public function formatStateUsing(\Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function align(string $alignment): static
    {
        if (!in_array($alignment, ['left', 'center', 'right'], true)) {
            throw new \InvalidArgumentException(
            "Invalid column alignment: {$alignment}"
            );
        }

        $this->alignment = $alignment;

        return $this;

    }

    public function alignment(): string
    {
        return $this->alignment;
    }

    protected function format(mixed $state, mixed $row): mixed
    {
        if ($this->formatStateUsing) {

            return call_user_func(
                $this->formatStateUsing,
                $state,
                $row
            );
        }

        return $state;
    }

    abstract public function render($row);
}