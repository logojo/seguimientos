<?php

namespace App\Livewire\Shared;

use App\Support\DataTable\Columns\IconColumn;
use App\Support\DataTable\Columns\RelationColumn;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

abstract class DataTable extends Component
{
    use WithPagination;

    // ── Estado ─────────────────────────────────────────────────────────────
    public array $filters = [];

    #[Url(as: 's')]
    public string $sortColumn = '';

    #[Url(as: 'd')]
    public string $sortDirection = 'asc';

    public array $hiddenColumns = [];

    #[Url(as: 'pp')]
    public int $perPage = 10;


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
        'icon' => [
            'null' => 'Esta',
        ],
    ];


    #[Url(as: 'v')]
    public string $viewToken = '';

    public function mount(): void
    {
        if ($this->viewToken) {

            $state = Cache::get("datatable:view:{$this->viewToken}");

            if ($state) {
                $this->filters       = $state['filters'] ?? [];
                $this->sortColumn    = $state['sortColumn'] ?? '';
                $this->sortDirection = $state['sortDirection'] ?? 'asc';
                $this->perPage       = $state['perPage'] ?? 10;
                $this->hiddenColumns = $state['hiddenColumns'] ?? [];
            }
        }
    }


    // ── Métodos obligatorios ───────────────────────────────────────────────
    abstract protected function model(): string;
    abstract protected function columns(): array;

    #[On('render-table')]
    public function refreshTable(): void
    {
        $this->dispatch('$refresh');
    }

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
         $this->persistState();
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

        $this->persistState();
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        $this->persistState();
        $this->resetPage();
    }

    private function persistState(): void
    {
        $token = Str::random(8);

        Cache::put(
            "datatable:view:{$token}",
            [
                'filters' => $this->filters,
                'sortColumn' => $this->sortColumn,
                'sortDirection' => $this->sortDirection,
                'perPage' => $this->perPage,
                'hiddenColumns' => $this->hiddenColumns,
            ],
            now()->addMinutes(30) // ajustable
        );

        $this->viewToken = $token;
    }

     public function updatedFilters(): void
    {
        $this->persistState();
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

        $this->persistState();
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->persistState();
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

        $this->persistState();

    }


    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function updatingSortColumn()
    {
        $this->resetPage();
    }

    public function updatingSortDirection()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // end manejo de paginacion

    private function applyOperator($query, $column, $op, $value)
    {
      
        return match ($op) {
            'contains' => $query->where($column, 'ilike', "%$value%"),
            'equals'   => $query->where($column, '=', $value),
            'starts'   => $query->where($column, 'ilike', "$value%"),
            'ends'     => $query->where($column, 'ilike', "%$value"),
            'not'      => $query->where($column, '!=', "$value"),
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

                if ($col instanceof RelationColumn) {

                    [$rel, $relCol] = explode('.', $col->relation);

                    $q->whereHas($rel, function ($q2) use ($relCol, $op, $value) {
                        $this->applyOperator($q2, $relCol, $op, $value);
                    });

                } 
                elseif($col instanceof IconColumn) {                        
                        if( $value == 'edit' )
                            $q->whereColumn('updated_at', '>', 'created_at');                         
                        else
                            $q->whereColumn('updated_at', '=', 'created_at');
                }
                else {
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

        return $this->buildQuery()->paginate(
            $this->perPage,
            pageName: 'page'
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