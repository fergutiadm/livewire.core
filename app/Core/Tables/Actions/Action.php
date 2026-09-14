<?php

namespace App\Core\Tables\Actions;

abstract class Action
{
    public function __construct(
        public string $label,
        public ?string $icon = null
    ) {}

    abstract public function handle($row);
}