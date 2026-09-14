<?php

namespace App\Modules\Comercial\Cliente\Policies;

class ClientePolicy
{
    public function viewAny(): bool { return true; }
    public function view(): bool { return true; }
    public function create(): bool { return true; }
    public function update(): bool { return true; }
    public function delete(): bool { return true; }
}