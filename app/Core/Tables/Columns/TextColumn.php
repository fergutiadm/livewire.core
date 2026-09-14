<?php

namespace App\Core\Tables\Columns;

class TextColumn extends Column
{
    protected ?\Closure $formatStateUsing = null;

    public function formatStateUsing(\Closure $callback): static
    {
        $this->formatStateUsing = $callback;

        return $this;
    }

    public function render($row): mixed
    {
        $state = data_get($row, $this->field);

        $state = $this->format($state, $row);

        return e($state);
    }
}