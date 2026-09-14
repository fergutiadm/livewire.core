<?php

namespace App\Core\Tables;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseTable
{
    protected int $perPage = 10;

    protected string $sortField = 'id';

    protected string $sortDirection = 'desc';

    protected string $search = '';

    protected array $filters = [];

    abstract public function query(): Builder;

    abstract public function columns(): array;

    protected array $parameters = [];

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Helper opcional
     */
    public function getParameter(
        string $key,
        mixed $default = null,
    ): mixed {
        return $this->parameters[$key] ?? $default;
    }

    public function setState(array $state): void
    {
        $this->search = $state['search'] ?? '';

        $this->sortField = $state['sortField'] ?? 'id';

        $this->sortDirection = $state['sortDirection'] ?? 'desc';

        $this->filters = $state['filters'] ?? [];

        $this->perPage = $state['perPage'] ?? 10;
    }

    protected function searchable(): array
    {
        return [];
    }

    public function getSearchable(): array
    {
        return $this->searchable();
    }

    protected function applySearch(Builder $query): Builder
    {
        if (blank($this->search)) {
            return $query;
        }

        return $query->where(function ($q) {
            foreach ($this->getSearchable() as $i => $field) {
                if ($i === 0) {
                    $q->where($field, 'like', "%{$this->search}%");
                } else {
                    $q->orWhere($field, 'like', "%{$this->search}%");
                }
            }
        });
    }

    protected function applyFilters(Builder $query): Builder
    {
        return $query;
    }

    protected function applySorting(Builder $query): Builder
    {
        return $query->orderBy(
            $this->sortField,
            $this->sortDirection
        );
    }

    protected function applyQueryPipeline(Builder $query): Builder
    {
        $query = $this->applySearch($query);

        $query = $this->applyFilters($query);

        $query = $this->applySorting($query);

        return $query;
    }

    public function getRows(): LengthAwarePaginator
    {

        $query = $this->query();

        // logger()->info('BASE TABLE - query()', [
        //     'table' => static::class,
        //     'elapsed_ms' => round((microtime(true) - $start) * 1000, 2),
        // ]);

        // $query = $this->applyQueryPipeline($query);

        // logger()->info('BASE TABLE - pipeline', [
        //     'table' => static::class,
        //     'elapsed_ms' => round((microtime(true) - $start) * 1000, 2),
        //     'sql' => $query->toSql(),
        //     'bindings' => $query->getBindings(),
        // ]);

        $rows = $query->paginate($this->perPage);

        return $rows;
    }

    public function getColumns(): array
    {
        return $this->columns();
    }

    public function getState(): array
    {
        return [
            'search' => $this->search,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'filters' => $this->filters,
            'perPage' => $this->perPage,
        ];
    }
}
