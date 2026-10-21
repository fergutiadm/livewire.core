<?php

namespace App\Core\Context;

class LocalContext
{
    public function id(): ?int
    {
        return session('local_id');
    }

    public function set(int $localId): void
    {
        session(['local_id' => $localId]);
    }

    public function clear(): void
    {
        session()->forget('local_id');
    }
}