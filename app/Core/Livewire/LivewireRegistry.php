<?php

namespace App\Core\Livewire;

class LivewireRegistry
{
    private static array $components = [];

    public static function register(string $alias, string $class): void
    {
        self::$components[$alias] = $class;
    }

    public static function get(string $alias): ?string
    {
        return self::$components[$alias] ?? null;
    }

    public static function all(): array
    {
        return self::$components;
    }

    public static function exists(string $alias): bool
    {
        return isset(self::$components[$alias]);
    }
}