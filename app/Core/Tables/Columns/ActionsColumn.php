<?php

namespace App\Core\Tables\Columns;

use App\Core\Tables\Actions\Action;

class ActionsColumn extends Column
{
    protected array $actions = [];

    public function actions(array $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function render($row)
    {
        $html = collect($this->actions)

            ->map(fn (Action $action) => $action->handle($row))

            ->implode('');

        return "
            <div class='flex items-center justify-end gap-2'>
                {$html}
            </div>
        ";
    }
}