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

    //operadores por tipo
    public array $operators = [
        'string' => [
            'contains' => 'Contiene',
            'equals' => 'Es igual a',
            'starts' => 'Empieza con',
            'ends' => 'Termina con',
        ],
        'numeric' => [  
            'equals' => '=',
            'gt' => '>',
            'lt' => '<',
        ],
        'enum' => [
            'equals' => 'Es',
            'not' => 'No es',
        ],
    ];

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

    private function applyOperator($query, $column, $op, $value)
    {
        return match ($op) {
            'contains' => $query->where($column, 'ilike', "%$value%"),
            'equals'   => $query->where($column, '=', $value),
            'starts'   => $query->where($column, 'ilike', "$value%"),
            'ends'     => $query->where($column, 'ilike', "%$value"),
            'gt'       => $query->where($column, '>', $value),
            'lt'       => $query->where($column, '<', $value),
            default    => $query->where($column, 'ilike', "%$value%"),
        };
    }

    // ── Query builder separado ─────────────────────────────────────────────
    protected function buildQuery(): Builder
    {
        $query = ($this->model())::query();

        if ($with = $this->with()) {
            $query->with($with);
        }

        foreach ($this->filters as $filter) {

            if (blank($filter['value'])) continue;

            $col = collect($this->columnsDef())
                ->firstWhere('key', $filter['col']);

            if (!$col) continue;

            $column = $col->key;
            $op     = $filter['operator'] ?? 'contains';
            $value  = $filter['value'];

            $query->where(function ($q) use ($col, $column, $op, $value) {

                if ($col instanceof \App\Support\DataTable\RelationColumn) {

                    [$rel, $relCol] = explode('.', $col->relation);

                    $q->whereHas($rel, function ($q2) use ($relCol, $op, $value) {
                        $this->applyOperator($q2, $relCol, $op, $value);
                    });

                } else {
                    $this->applyOperator($q, $column, $op, $value);
                }

            });
        }

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