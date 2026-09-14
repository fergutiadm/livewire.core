<?php

namespace App\Core\Tables;

use Livewire\Component;
use Livewire\WithPagination;

abstract class TableComponent extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    public int $perPage = 10;

    public array $filters = [];

    abstract protected function tableClass(): string;

    protected function tableParameters(): array
    {
        return [];
    }

    protected $queryString = [
        'page' => ['except' => 1],
    ];

    public function mount(): void
    {
        $this->resetPage();
    }

    /**
     * Puede provocar una recursión/ciclo en el manejo de la paginación
     */
    // public function updatingPage($page): void
    // {
    //     $this->resetPage();
    // }

    /*
    |--------------------------------------------------------------------------
    | TABLE FACTORY (STATELESS)
    |--------------------------------------------------------------------------
    */

    protected function table(): BaseTable
    {
        $class = $this->tableClass();

        if (! class_exists($class)) {
            throw new \RuntimeException(
                "Table class not found: {$class}"
            );
        }

        $parameters = $this->tableParameters();

        $table = app($class, [
            'parameters' => $parameters,
        ]);


        if (! $table instanceof BaseTable) {
            throw new \RuntimeException(
                "Invalid table class: {$class}"
            );
        }

        $table->setParameters($parameters);
        $table->setState([
            'search' => $this->search,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'filters' => $this->filters,
            'perPage' => $this->perPage,
        ]);

        return $table;
    }

    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /*
    |--------------------------------------------------------------------------
    | SORTING
    |--------------------------------------------------------------------------
    */

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {

            $this->sortDirection =
                $this->sortDirection === 'asc'
                    ? 'desc'
                    : 'asc';

        } else {

            $this->sortField = $field;

            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    protected function tableOptions(): array
    {
        return [];
    }

    protected function sortable(): bool
    {
        return false;
    }

    protected function sortableMethod(): ?string
    {
        return null;
    }

    protected function sortableOptions(): array
    {
        return [
            'animation' => 150,
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render()
    {
        $table = $this->table();

        $rows = $table->getRows();
        $columns = $table->getColumns();

        return view('components.ui.table.table-component', [
            'rows' => $rows,
            'columns' => $columns,
            'tableOptions' => $this->tableOptions(),
            'sortable' => $this->sortable(),
            'sortableMethod' => $this->sortableMethod(),
            'sortableOptions' => $this->sortableOptions(),
        ]);
    }
}
