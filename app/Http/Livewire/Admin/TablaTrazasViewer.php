<?php

namespace App\Http\Livewire\Admin;

use App\Models\Traza;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Component;

use Livewire\Attributes\Reactive;

class TablaTrazasViewer extends Component
{
    use WithPagination;

    #[Reactive]
    public ?string $search = '';

    #[Reactive]
    public ?string $modelFilter = '';

    #[Reactive]
    public ?string $userFilter = '';

    #[Reactive]
    public ?string $dateFrom = '';

    #[Reactive]
    public ?string $dateTo = '';

    public $perPage = 20;

    // public function mount(
    //                         ?string $search = '',
    //                         ?string $modelFilter = '',
    //                         ?string $userFilter = '',
    //                         ?string $dateFrom = '',
    //                         ?string $dateTo = '',
    //                      )
    // {
    //     $this->filtrar($search, $modelFilter, $userFilter, $dateFrom, $dateTo);
    //     \Log::info('TrazasViewer::mount(...)', [
    //         'search' => $this->search,
    //         'modelFilter' => $this->modelFilter,
    //         'userFilter' => $this->userFilter,
    //         'dateFrom' => $this->dateFrom,
    //         'dateTo' => $this->dateTo,
    //         ]);
    // }

    //#[On('filtrar-trazas')]
    // public function filtrar(
    //                         ?string $search = '',
    //                         ?string $modelFilter = '',
    //                         ?string $userFilter = '',
    //                         ?string $dateFrom = '',
    //                         ?string $dateTo = '',
    //                      )
    // {
    //     $this->search = $search;
    //     $this->modelFilter = $modelFilter;
    //     $this->userFilter = $userFilter;
    //     $this->dateFrom = $dateFrom;
    //     $this->dateTo = $dateTo;

    //     \Log::info('TrazasViewer::filtrar - HIJO', [
    //         'search' => $this->search,
    //         'modelFilter' => $this->modelFilter,
    //         'userFilter' => $this->userFilter,
    //         'dateFrom' => $this->dateFrom,
    //         'dateTo' => $this->dateTo,
    //         ]);
    // }

    public function render()
    {
        $query = Traza::query()
        ->with('trazable', 'user')
        ->orderByDesc('created_at');


        if ($this->search) {
            $query->where('operacion', 'like', "%{$this->search}%");
        }

        if ($this->modelFilter) {
            $query->where('trazable_type', $this->modelFilter);
        }

        if ($this->userFilter) {
            $query->where('user_id', $this->userFilter);
        }

        if ($this->dateTo) {
            \Log::info('TablaTrazasViewer::render - DATE-TO');
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->dateFrom) {
            \Log::info('TablaTrazasViewer::render - DATE-FORM');
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        \Log::info('TablaTrazasViewer::render - QUERY', [
            'search' => $this->search,
            'modelFilter' => $this->modelFilter,
            'userFilter' => $this->userFilter,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        $trazas = $query->paginate($this->perPage);

        \Log::info('TablaTrazasViewer::render - RESULTADO', [
            'count' => $trazas->count(),
            'total' => $trazas->total(),
            'first_created_at' => $trazas->first()?->created_at?->toDateTimeString(),
            'last_created_at' => $trazas->last()?->created_at?->toDateTimeString(),
        ]);

        return view('livewire.admin.tabla-trazas-viewer', [
            'trazas' => $trazas,
        ]);

    }

}
