<?php

namespace App\Modules\Admin\Cierre\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Core\CQRS\HasCommands;

class CierrePage extends Component
{
    use HasCommands;

    public bool $showForm = true;
    public int $tableVersion = 0;

    #[On('cierre-guardado')]
    public function onSaved(): void
    {
        $this->tableVersion++;
    }

    public function render()
    {
        return view('modules.admin.cierre.page', [
            'tableVersion' => $this->tableVersion,
        ])->layout('layouts.app_admin');
    }
}