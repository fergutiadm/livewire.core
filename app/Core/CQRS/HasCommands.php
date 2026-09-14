<?php

namespace App\Core\CQRS;

trait HasCommands
{
    protected function command(
        string $command,
        array $payload = []
    ) {

        return app(CommandBus::class)
            ->dispatch(
                $command,
                $payload
            );
    }
}