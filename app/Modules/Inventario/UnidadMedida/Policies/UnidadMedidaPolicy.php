<?php

namespace App\Modules\Inventario\UnidadMedida\Policies;

class UnidadMedidaPolicy
{
    public function viewAny(): bool { return true; }
    public function view(): bool { return true; }
    public function create(): bool { return true; }
    public function update(): bool { return true; }
    public function delete(): bool { return true; }
}