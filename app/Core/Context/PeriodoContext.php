<?php

namespace App\Core\Context;

class PeriodoContext
{
    public function id(): ?int
    {
        return session('periodo_id');
    }

    public function set(int $periodoId): void
    {
        session(['periodo_id' => $periodoId]);
    }

    public function clear(): void
    {
        session()->forget('periodo_id');
    }
}