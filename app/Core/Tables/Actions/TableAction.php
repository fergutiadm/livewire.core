<?php

namespace App\Core\Tables\Actions;

class TableAction extends Action
{
    public string $color = 'gray';
    public string $event;
    public ?string $icon = null;
    public ?string $loadingMessage = null;

    protected static array $colorMap = [
        'gray'   => 'bg-gray-500 hover:bg-gray-600',
        'red'    => 'bg-red-500 hover:bg-red-600',
        'amber'  => 'bg-amber-500 hover:bg-amber-600',
        'yellow' => 'bg-yellow-500 hover:bg-yellow-600',
        'blue'   => 'bg-blue-500 hover:bg-blue-600',
        'green'  => 'bg-green-500 hover:bg-green-600',
        'indigo' => 'bg-indigo-500 hover:bg-indigo-600',
    ];

    public function color(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function event(string $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function loading(string $message): static
    {
        $this->loadingMessage = $message;

        return $this;
    }

    protected function colorClass(): string
    {
        return self::$colorMap[$this->color] ?? self::$colorMap['gray'];
    }

    public function handle($row): string
    {
        return view('components.ui.table.actions.button', [
            'label'          => $this->label,
            'event'          => $this->event,
            'icon'           => $this->icon,
            'row'             => $row,
            'class'           => $this->colorClass(),
            'loadingMessage' => $this->loadingMessage,
        ])->render();
    }
}