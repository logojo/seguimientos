<?php

namespace App\Livewire\Shared;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

abstract class DataTable extends Component
{
    // ── Estado serializable ────────────────────────────────────────────────
    #[Url(as: 'fop')]
    public string $filterOperator = 'AND';

    public array  $filters        = [];

    #[Url(as: 's')]
    public string $sortColumn     = '';

    public string $sortDirection  = 'asc';

    #[Url(as: 'p')]
    public int    $page           = 1;

    #[Url(as: 'cols')]
    public array  $hiddenColumns  = [];

    #[Url(as: 'pp')]
    public int    $perPage        = 10;
    public string $title          = '';

    #[Url(as: 'f')]
    public string $filtersQuery = '';

    // ── Cada hijo define estos métodos ─────────────────────────────────────
    abstract protected function model(): string;
    abstract protected function columns(): array;

    // ── Operadores disponibles por tipo de columna ─────────────────────────
    public array $stringOperators = [
        'contains'    => 'Contiene',
        'not_contains'=> 'No contiene',
        'equals'      => 'Es igual a',
        'not_equals'  => 'No es igual a',
        'starts_with' => 'Empieza con',
        'ends_with'   => 'Termina con',
        'not_null'    => 'No es nulo',
    ];

    public array $numericOperators = [
        'equals'  => 'Es igual a',
        'gt'      => 'Mayor que',
        'gte'     => 'Mayor o igual que',
        'lt'      => 'Menor que',
        'lte'     => 'Menor o igual que',
    ];

    public function mount(): void
    {
        // Restaurar filtros desde la URL
        if ($this->filtersQuery) {
           try {
                $decoded = json_decode(base64_decode($this->filtersQuery), true, flags: JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    $this->filters = $decoded;
                }
            } catch (\Throwable $e) {
                $this->filters = [];
            }

            // Seguridad básica (evita datos corruptos)
            if (is_array($decoded)) {
                $this->filters = $decoded;
            }
        }
    }

    protected function searchable(): array
    {
        return array_keys($this->columns());
    }

    protected function with(): array
    {
        return [];
    }

    // ── Watchers ───────────────────────────────────────────────────────────
    public function updatedFilterOperator(): void  { $this->resetPage(); }

    public function updatedFilters(): void
    {
        $this->syncFiltersToUrl();
        $this->resetPage();
    }

    private function syncFiltersToUrl(): void
    {
        $this->filtersQuery = rtrim(strtr(base64_encode(json_encode($this->filters)), '+/', '-_'), '=');
        //$this->filtersQuery = json_decode(base64_decode(strtr($this->filtersQuery, '-_', '+/')), true);
    }

    public function updateFilter(int $index, string $field, string $value): void
    {
        $this->filters[$index][$field] = $value;

        if ($field === 'col') {
            $colDef = $this->columns()[$value] ?? [];

            $this->filters[$index]['operator'] =
                ($colDef['type'] ?? 'string') === 'numeric'
                    ? 'equals'
                    : 'contains';
        }

        $this->syncFiltersToUrl();
        $this->resetPage();
    }

    // ── Acciones: filtros ──────────────────────────────────────────────────
    public function addFilter(): void
    {
        $this->filters[] = [
            'col' => $this->searchable()[0] ?? array_key_first($this->columns()),
            'operator' => 'contains',
            'value'    => '',
        ];

        $this->syncFiltersToUrl();
    }

    public function removeFilter(int $index): void
    {
        array_splice($this->filters, $index, 1);
        $this->filters = array_values($this->filters);
        $this->resetPage();

        $this->syncFiltersToUrl();
    }

    public function clearFilters(): void
    {
        $this->filters = [];
        $this->syncFiltersToUrl();
        $this->resetPage();
    }

    // ── Acciones: ordenamiento ─────────────────────────────────────────────
    public function sort(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleColumn(string $key): void
    {
        if (in_array($key, $this->hiddenColumns)) {
            $this->hiddenColumns = array_values(
                array_filter($this->hiddenColumns, fn($k) => $k !== $key)
            );
        } else {
            $this->hiddenColumns[] = $key;
        }
    }

    public function resetPage(): void
    {
        $this->page = 1;
    }

    // ── Filtrado en relación ───────────────────────────────────────────────────
    // Usa whereHas que es seguro y no rompe con JOINs posteriores
    private function applyRelationFilter(
        Builder $query,
        string  $relation,      // 'relacion con tabla'
        string  $relColumn,     // 'nombre_completo'
        string  $operator,
        string  $value,
        string  $method = 'where'  // 'where' | 'orWhere'
    ): Builder {
        $hasMethod = $method === 'orWhere' ? 'orWhereHas' : 'whereHas';

        return $query->{$hasMethod}(
            $relation,
            fn(Builder $q) => $this->applyOperator($q, $relColumn, $operator, $value)
        );
    }

    // ── Ordenamiento en relación ───────────────────────────────────────────────
    // Usa joinSub o addSelect + subquery para evitar JOINs duplicados
    private function applySortWithRelation(Builder $query, array $colDef): Builder
    {
        $parts    = explode('.', $colDef['relation']);  // ['relacion', 'nombre_completo']
        $relation = $parts[0];   // 'relacion'
        $relCol   = $parts[1];   // 'nombre_completo'

        // Obtenemos la instancia de la relación para leer FK y tabla destino
        $model        = new ($this->model);
        $relationObj  = $model->{$relation}();

        // Solo manejamos BelongsTo y HasOne (los más comunes en tablas)
        $relatedTable = $relationObj->getRelated()->getTable();
        $foreignKey   = match (true) {
            $relationObj instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo
                => $relationObj->getForeignKeyName(),          // 'medico_id' en consultas
            $relationObj instanceof \Illuminate\Database\Eloquent\Relations\HasOne
                => $relationObj->getForeignKeyName(),          // 'consulta_id' en la tabla hija
            default => null,
        };

        if (! $foreignKey) {
            // Para relaciones no soportadas simplemente ignoramos el sort
            return $query;
        }

        $alias     = '_sort_'.$relation;
        $ownerKey  = $relationObj instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo
            ? $relationObj->getOwnerKeyName()   // 'id' en medicos
            : $relationObj->getLocalKeyName();  // 'id' en consultas

        $localTable = $model->getTable();

        // LEFT JOIN con alias para evitar colisión si ya existe el JOIN
        // Sólo lo añadimos si no está ya en la query
        $joins = collect($query->getQuery()->joins ?? [])
            ->pluck('table')
            ->toArray();

        if (! in_array("{$relatedTable} as {$alias}", $joins)) {
            $query->leftJoin(
                "{$relatedTable} as {$alias}",
                "{$alias}.{$ownerKey}",
                '=',
                "{$localTable}.{$foreignKey}"
            );
        }

        return $query
            ->addSelect("{$localTable}.*")   // evita columnas ambiguas del JOIN
            ->orderBy("{$alias}.{$relCol}", $this->sortDirection);
    }

    // ── Query computed ─────────────────────────────────────────────────────
    #[Computed]
    public function rows(): LengthAwarePaginator
    {
        $query   = ($this->model())::query();
        $columns = $this->columns();

        if (count($this->with())) {
            $query->with($this->with());
        }

        // ── Filtros múltiples ──────────────────────────────────────────────
        $activeFilters = array_filter(
            $this->filters,
            fn($f) => trim($f['value'] ?? '') !== ''
        );

        if (count($activeFilters)) {
            $query->where(function (Builder $q) use ($activeFilters, $columns) {
                foreach ($activeFilters as $filter) {
                    $colDef   = $columns[$filter['col']] ?? null;
                    $operator = $filter['operator'] ?? 'contains';
                    $value    = $filter['value'];
                    $method   = $this->filterOperator === 'OR' ? 'orWhere' : 'where';

                    if (isset($colDef['relation'])) {
                        [$rel, $relCol] = explode('.', $colDef['relation']);

                        // ✅ Usa el método dedicado a relaciones
                        $this->applyRelationFilter($q, $rel, $relCol, $operator, $value, $method);
                    } else {
                        $dbCol = $colDef['column'] ?? $filter['col'];
                        $this->applyOperator($q, $dbCol, $operator, $value, $method);
                    }
                }
            });
        }

        // ── Ordenamiento ──────────────────────────────────────────────────
        if ($this->sortColumn !== '') {
            $colDef = $this->columns()[$this->sortColumn] ?? null;

            if (isset($colDef['relation'])) {
                // ✅ Usa LEFT JOIN con alias seguro
                $this->applySortWithRelation($query, $colDef);
            } else {
                $dbCol = $colDef['column'] ?? $this->sortColumn;
                $query->orderBy($dbCol, $this->sortDirection);
            }
        }

        //dd($query->get());
        return $query->paginate($this->perPage, page: $this->page);
    }

    // ── Aplicar operador al query builder ──────────────────────────────────
    private function applyOperator(
        Builder $query,
        string  $column,
        string  $operator,
        string  $value,
        string  $method = 'where'
    ): Builder {
        return match ($operator) {
            'contains'     => $query->{$method}($column, 'ilike', "%{$value}%"),
            'not_contains' => $query->{$method}($column, 'not ilike', "%{$value}%"),
            'equals'       => $query->{$method}($column, '=', $value),
            'not_equals'   => $query->{$method}($column, '!=', $value),
            'starts_with'  => $query->{$method}($column, 'ilike', "{$value}%"),
            'ends_with'    => $query->{$method}($column, 'ilike', "%{$value}"),
            'not_null'      => $method === 'orWhere'
                                       ? $query->orWhereNotNull($column)
                                       : $query->whereNotNull($column),
            'gt'           => $query->{$method}($column, '>', $value),
            'gte'          => $query->{$method}($column, '>=', $value),
            'lt'           => $query->{$method}($column, '<', $value),
            'lte'          => $query->{$method}($column, '<=', $value),
            default        => $query->{$method}($column, 'ilike', "%{$value}%"),
        };
    }

    // ── Helpers ────────────────────────────────────────────────────────────
    public function visibleColumns(): array
    {
        return array_filter(
            $this->columns(),
            fn($key) => ! in_array($key, $this->hiddenColumns),
            ARRAY_FILTER_USE_KEY
        );
    }

    public function getCellValue(object $row, string $key): mixed
    {
        $colDef = $this->columns()[$key];

        if (isset($colDef['render']) && $colDef['render'] instanceof \Closure) {
            return ($colDef['render'])($row);
        }

        if (isset($colDef['relation'])) {
            [$rel, $relCol] = explode('.', $colDef['relation']);
            return $row->{$rel}?->{$relCol} ?? '—';
        }

        $value =  $row->{$colDef['column'] ?? $key} ?? '—';

        // ✅ Convierte Enums a su valor escalar automáticamente
            if ($value instanceof \BackedEnum) {
                return $value->value;
            }

            if ($value instanceof \UnitEnum) {
                return $value->name;
            }

            return $value;
            }

    // Retorna los operadores válidos según el tipo de columna
    public function getOperatorsFor(string $colKey): array
    {
        $type = $this->columns()[$colKey]['type'] ?? 'string';
        return $type === 'numeric' ? $this->numericOperators : $this->stringOperators;
    }

    public function activeFilterCount(): int
    {
        return count(array_filter($this->filters, fn($f) => trim($f['value'] ?? '') !== ''));
    }

    public function render()
    {
        return view('livewire.shared.data-table', [
            'columns'    => $this->columns(),
            'searchable' => $this->searchable(),
        ]);
    }
}
