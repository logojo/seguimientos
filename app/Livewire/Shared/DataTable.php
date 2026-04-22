<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class DataTable extends Component
{
    // ── Estado ─────────────────────────────────────────────────────────────
    public array $filters = [];
    public string $sortColumn = '';
    public string $sortDirection = 'asc';
    public array $hiddenColumns = [];
    public int $perPage = 10;
    public int $page = 1;

    // ── Métodos obligatorios ───────────────────────────────────────────────
    abstract protected function model(): string;
    abstract protected function columns(): array;

    protected function with(): array
    {
        return [];
    }

    // ── Columnas cacheadas ─────────────────────────────────────────────────
    #[Computed]
    public function columnsDef(): array
    {
        return $this->columns();
    }

    // ── Columnas visibles ──────────────────────────────────────────────────
    public function visibleColumns(): array
    {
        return array_filter(
            $this->columnsDef(),
            fn ($col) => !in_array($col->key, $this->hiddenColumns)
        );
    }

    // ── Filtros ────────────────────────────────────────────────────────────
    public function addFilter(): void
    {
        $firstColumn = $this->columnsDef()[0] ?? null;

        if (!$firstColumn) return;

        $this->filters[] = [
            'col' => $firstColumn->key,
            'value' => '',
        ];

        $this->resetPage();
    }

    public function updateFilter(int $index, string $field, string $value): void
    {
        $this->filters[$index][$field] = $value;
        $this->resetPage();
    }

    public function removeFilter(int $index): void
    {
        array_splice($this->filters, $index, 1);
        $this->filters = array_values($this->filters);

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        $this->resetPage();
    }

    // ── Sorting ────────────────────────────────────────────────────────────
    public function sort(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    // ── Column toggle ──────────────────────────────────────────────────────
    public function toggleColumn(string $key): void
    {
        if (in_array($key, $this->hiddenColumns)) {
            $this->hiddenColumns = array_values(
                array_filter($this->hiddenColumns, fn ($k) => $k !== $key)
            );
        } else {
            $this->hiddenColumns[] = $key;
        }
    }

    public function resetPage(): void
    {
        $this->page = 1;
    }

    // ── Query builder separado ─────────────────────────────────────────────
    protected function buildQuery(): Builder
    {
        $query = ($this->model())::query();

        if ($with = $this->with()) {
            $query->with($with);
        }

        // ── Filtros simples ───────────────────────────────────────────────
        foreach ($this->filters as $filter) {
            if (!isset($filter['value']) || trim($filter['value']) === '') {
                continue;
            }

            $query->where($filter['col'], 'like', "%{$filter['value']}%");
        }

        // ── Sorting ───────────────────────────────────────────────────────
        if ($this->sortColumn) {
            $query->orderBy($this->sortColumn, $this->sortDirection);
        }

        return $query;
    }

    // ── Rows con cache ligera ──────────────────────────────────────────────
    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $key = md5(json_encode([
            $this->filters,
            $this->sortColumn,
            $this->sortDirection,
            $this->page,
            $this->perPage,
        ]));

        return Cache::remember(
            "datatable:$key",
            now()->addSeconds(5),
            fn () => $this->buildQuery()->paginate(
                $this->perPage,
                page: $this->page
            )
        );
    }

    // ── Render ─────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.shared.data-table', [
            'columns' => $this->columnsDef(),
        ]);
    }
}