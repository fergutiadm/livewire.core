<?php

namespace App\Core\Tables\Columns;

class BadgeColumn extends Column
{
    public function __construct(
        string $label,
        string $field,
        protected array $colors = []
    ) {
        parent::__construct($label, $field);
    }

    public function render($row)
    {
        $state = data_get($row, $this->field);

        $state = $this->format($state, $row);

        $color = $this->colors[$state] ?? 'gray';

        return "
            <span class='
                inline-flex
                items-center
                rounded-full
                px-2.5
                py-1
                text-xs
                font-medium
                bg-{$color}-100
                text-{$color}-700
            '>
                {$state}
            </span>
        ";
    }
}