<?php

namespace App\Core\Tables\Columns;

class HtmlColumn extends Column
{
    public function render($row): mixed
    {
        $state = data_get($row, $this->field);

        return $this->format($state, $row);
    }
}
