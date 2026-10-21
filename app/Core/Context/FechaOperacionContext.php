<?php

namespace App\Core\Context;

class FechaOperacionContext
{
    public function fecha(): ?string
    {
        return session('fecha_operacion');
    }

    public function set(string $fecha_operacion): void
    {
        session(['fecha_operacion' => $fecha_operacion]);
    }

    public function clear(): void
    {
        session()->forget('fecha_operacion');
    }
}