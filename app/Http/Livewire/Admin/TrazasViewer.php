<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
//use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\Traza;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TrazasViewer extends Component
{
    //use WithPagination;

    public $search = '';
    public $modelFilter = '';
    public $userFilter = '';
    public $dateFrom = '';
    public $dateTo = '';

    public $models = [];  // Para select de modelos
    public $users = [];   // Para select de usuarios

    public $perPage = 20;

    public int $tableVersion = 0;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // Cargar select de modelos y usuarios una sola vez
        $this->models = Traza::select('trazable_type')->distinct()->pluck('trazable_type')->sort()->toArray();
        $this->users  = User::orderBy('name')->get();
    }

    public function render()
    {
        $query = Traza::query()->with('trazable', 'user')->orderByDesc('created_at')    ;

        if ($this->search) {
            $query->where('operacion', 'like', "%{$this->search}%");
        }

        if ($this->modelFilter) {
            $query->where('trazable_type', $this->modelFilter);
        }

        if ($this->userFilter) {
            $query->where('user_id', $this->userFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        // \Log::info('TablaTrazasViewer::render - QUERY', [
        //     'search' => $this->search,
        //     'modelFilter' => $this->modelFilter,
        //     'userFilter' => $this->userFilter,
        //     'dateFrom' => $this->dateFrom,
        //     'dateTo' => $this->dateTo,
        //     'sql' => $query->toSql(),
        //     'bindings' => $query->getBindings(),
        // ]);

        $trazas = $query->paginate($this->perPage);

        return view('livewire.admin.trazas-viewer', compact('trazas'))
                ->layout('layouts.app_admin');
    }
}
