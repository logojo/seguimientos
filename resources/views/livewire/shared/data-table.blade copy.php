<div class="flex flex-col gap-3">

    {{-- ═══ TOOLBAR PRINCIPAL ═══ --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Botón añadir filtro --}}
        <button wire:click="addFilter" class="btn btn-sm btn-outline gap-2">
            <span class="material-symbols-outlined" style='font-size:15px'>add</span>
            Añadir filtro
            @if($this->activeFilterCount() > 0)
                <span class="badge badge-primary badge-sm">{{ $this->activeFilterCount() }}</span>
            @endif
        </button>

        {{-- Operador AND / OR --}}
        @if(count($filters) > 1)
            <div class="join" title="Cómo se combinan los filtros">
                <button
                    wire:click="$set('filterOperator', 'AND')"
                    class="join-item btn btn-xs {{ $filterOperator === 'AND' ? 'btn-primary' : 'btn-outline' }}"
                >Y</button>
                <button
                    wire:click="$set('filterOperator', 'OR')"
                    class="join-item btn btn-xs {{ $filterOperator === 'OR' ? 'btn-primary' : 'btn-outline' }}"
                >O</button>
            </div>

            <span class="text-xs text-base-content/50">
                @if($filterOperator === 'AND')
                    Todos los filtros deben cumplirse
                @else
                    Al menos un filtro debe cumplirse
                @endif
            </span>
        @endif

        {{-- Columnas + contador --}}
        <div class="ml-auto flex items-center gap-2">
            <span class="text-xs text-base-content/50">
                {{ $this->rows->total() }} registros
            </span>

            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-outline btn-sm gap-1.5">
                    <span class="material-symbols-outlined" style="font-size: 15px">grid_view</span>
                    Columnas
                </div>
                <ul tabindex="0"
                    class="dropdown-content menu bg-base-100 rounded-box border border-base-300 z-50 w-52 p-2 shadow-sm mt-1">
                    <li class="menu-title text-xs">Mostrar / ocultar</li>
                    @foreach($columns as $key => $col)
                        <li>
                            <label class="label cursor-pointer justify-start gap-3 py-1.5">
                                <input
                                    type="checkbox"
                                    class="checkbox checkbox-primary checkbox-sm"
                                    wire:click="toggleColumn('{{ $key }}')"
                                    @checked(!in_array($key, $hiddenColumns))
                                >
                                <span class="label-text">{{ $col['label'] }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ═══ FILAS DE FILTROS ═══ --}}
    @if(count($filters) > 0)
        <div class="flex flex-col gap-2">
            @foreach($filters as $index => $filter)
                <div class="flex items-center gap-2 flex-wrap bg-base-200/50 border border-base-300 rounded-xl px-3 py-2"
                     wire:key="filter-{{ $index }}">

                    {{-- Etiqueta SI / AND / OR --}}
                    <span class="text-xs font-semibold w-8 shrink-0
                        {{ $index === 0
                            ? 'text-base-content/40'
                            : ($filterOperator === 'AND' ? 'text-primary' : 'text-warning') }}">
                        {{ 
                            $index === 0 
                            ? 'Sí' 
                            :$filterOperator
                        }}
                    </span>

                    {{-- Selector de columna --}}
                    <select
                        class="select select-bordered select-sm"
                        wire:change="updateFilter({{ $index }}, 'col', $event.target.value)"
                    >
                        @foreach($searchable as $colKey)
                            @if(isset($columns[$colKey]))
                                <option value="{{ $colKey }}"
                                    @selected(($filter['col'] ?? '') === $colKey)>
                                    {{ $columns[$colKey]['label'] }}
                                </option>
                            @endif
                        @endforeach
                    </select>

                    {{-- Selector de operador (depende del tipo de columna) --}}
                    <select
                        class="select select-bordered select-sm"
                        wire:change="updateFilter({{ $index }}, 'operator', $event.target.value)"
                    >
                        @foreach($this->getOperatorsFor($filter['col'] ?? array_key_first($columns)) as $opKey => $opLabel)
                            <option value="{{ $opKey }}"
                                @selected(($filter['operator'] ?? 'contains') === $opKey)>
                                {{ $opLabel }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Valor de búsqueda --}}
                    <input
                        type="text"
                        class="input input-bordered input-sm flex-1 min-w-32"
                        placeholder="Valor..."
                        wire:model.live.debounce.300ms="filters.{{ $index }}.value"
                    />

                    {{-- Eliminar filtro --}}
                    <button
                        wire:click="removeFilter({{ $index }})"
                        class="btn btn-ghost btn-sm btn-square text-base-content/40 hover:text-error"
                        title="Eliminar filtro"
                    >
                        <span class="material-symbols-outlined" style='font-size:15px'>close</span>
                    </button>
                </div>
            @endforeach

            {{-- Chips de filtros activos --}}
            @php $activeFilters = array_filter($filters, fn($f) => trim($f['value'] ?? '') !== ''); @endphp
            @if(count($activeFilters) > 0)
                <div class="flex flex-wrap items-center gap-2 px-1">
                    <span class="text-xs text-base-content/40">Activos:</span>

                    @foreach($activeFilters as $idx => $f)
                        @if($idx > array_key_first($activeFilters))
                            <span class="badge badge-sm {{ $filterOperator === 'AND' ? 'badge-primary' : 'badge-warning' }}">
                                {{ $filterOperator }}
                            </span>
                        @endif
                        <span class="badge badge-outline badge-sm gap-1.5">
                            <span class="font-semibold">{{ $columns[$f['col']]['label'] ?? $f['col'] }}</span>
                            <span class="opacity-60">{{ $stringOperators[$f['operator']] ?? $f['operator'] }}</span>
                            "{{ $f['value'] }}"
                            <button
                                wire:click="removeFilter({{ $idx }})"
                                class="hover:text-error ml-1"
                            >×</button>
                        </span>
                    @endforeach

                    <button wire:click="clearFilters" class="text-xs text-base-content/40 hover:text-error underline">
                        Limpiar todo
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ TABLA ═══ --}}
    <div class="border border-base-300 rounded-box overflow-auto">
        <table class="table table-zebra table-xs md:table-md w-full">
            <thead class="bg-base-200/50">
                <tr>
                    @foreach($columns as $key => $col)
                        @continue(in_array($key, $hiddenColumns))
                        <th
                            wire:click="{{ ($col['sortable'] ?? true) ? 'sort(\''.$key.'\')' : '' }}"
                            class="{{ ($col['sortable'] ?? true) ? 'cursor-pointer hover:bg-base-300/40' : '' }}
                                   {{ $sortColumn === $key ? 'text-primary' : '' }}
                                   whitespace-nowrap select-none transition-colors uppercase text-xs"
                            style="{{ isset($col['width']) ? 'width:'.$col['width'] : '' }}"
                        >
                            <span class="inline-flex items-center gap-1.5">
                                {{ $col['label'] }}
                                @if($col['sortable'] ?? true)
                                    <span class="text-xs opacity-40">
                                        @if($sortColumn === $key)
                                            {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                        @else ↕ @endif
                                    </span>
                                @endif
                            </span>
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                <tr>
                    @foreach($this->visibleColumns() as $col)
                    <td>
                            @switch(true)
        
                                @case($col instanceof \App\Support\DataTable\Columns\BadgeColumn)
                                    <span class="badge {{ $col->colors[$row->{$col->key}] ?? 'badge-ghost' }}">
                                        {{ $row->{$col->key} }}
                                    </span>
                                @break
        
                                @case($col instanceof \App\Support\DataTable\Columns\ProgressColumn)
                                    <progress class="progress w-full" value="{{ $row->{$col->key} }}" max="100"></progress>
                                @break
        
                                @default
                                    {{ data_get($row, $col->key) }}
                            @endswitch
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>

        <div wire:loading wire:target="filters, filterOperator, sort, page, toggleColumn"
             class="flex items-center justify-center gap-2 py-3 text-sm text-base-content/40 border-t border-base-300">
            <span class="loading loading-spinner loading-xs"></span>
            Actualizando...
        </div>
    </div>

    {{-- ═══ PAGINACIÓN ═══ --}}
    @if($this->rows->lastPage() > 1)
        <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
            <span class="text-base-content/50">
                Mostrando
                <strong>{{ $this->rows->firstItem() }}–{{ $this->rows->lastItem() }}</strong>
                de <strong>{{ $this->rows->total() }}</strong>
            </span>
            <div class="join">
                <button
                    wire:click="$set('page', {{ max(1, $this->rows->currentPage() - 1) }})"
                    class="join-item btn btn-sm btn-outline"
                    @disabled($this->rows->onFirstPage())
                >‹</button>

                @for($i = max(1, $this->rows->currentPage() - 2); $i <= min($this->rows->lastPage(), $this->rows->currentPage() + 2); $i++)
                    <button
                        wire:click="$set('page', {{ $i }})"
                        class="join-item btn btn-sm {{ $i === $this->rows->currentPage() ? 'btn-primary' : 'btn-outline' }}"
                    >{{ $i }}</button>
                @endfor

                <button
                    wire:click="$set('page', {{ min($this->rows->lastPage(), $this->rows->currentPage() + 1) }})"
                    class="join-item btn btn-sm btn-outline"
                    @disabled(! $this->rows->hasMorePages())
                >›</button>
            </div>
        </div>
    @endif
</div>