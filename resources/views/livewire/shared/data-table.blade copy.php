<div class="flex flex-col gap-3">

    {{-- ═══ TOOLBAR PRINCIPAL ═══ --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Botón añadir filtro --}}
        <button wire:click="addFilter" class="btn btn-sm btn-outline gap-2">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
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
                >AND</button>
                <button
                    wire:click="$set('filterOperator', 'OR')"
                    class="join-item btn btn-xs {{ $filterOperator === 'OR' ? 'btn-primary' : 'btn-outline' }}"
                >OR</button>
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
                    <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                    </svg>
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
                        {{ $index === 0 ? 'si' : $filterOperator }}
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
                        value="{{ $filter['value'] ?? '' }}"
                        wire:change="updateFilter({{ $index }}, 'value', $event.target.value)"
                        wire:keyup.debounce.300ms="updateFilter({{ $index }}, 'value', $event.target.value)"
                    >

                    {{-- Eliminar filtro --}}
                    <button
                        wire:click="removeFilter({{ $index }})"
                        class="btn btn-ghost btn-sm btn-square text-base-content/40 hover:text-error"
                        title="Eliminar filtro"
                    >
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
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
        <table class="table table-zebra table-sm w-full">
            <thead class="bg-base-200/50">
                <tr>
                    @foreach($columns as $key => $col)
                        @continue(in_array($key, $hiddenColumns))
                        <th
                            wire:click="{{ ($col['sortable'] ?? true) ? 'sort(\''.$key.'\')' : '' }}"
                            class="{{ ($col['sortable'] ?? true) ? 'cursor-pointer hover:bg-base-300/40' : '' }}
                                   {{ $sortColumn === $key ? 'text-primary' : '' }}
                                   whitespace-nowrap select-none transition-colors"
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
                @forelse($this->rows as $row)
                    <tr class="hover">
                        @foreach($columns as $key => $col)
                            @continue(in_array($key, $hiddenColumns))
                            <td class="{{ $col['class'] ?? '' }}">
                                @if(isset($col['badge']))
                                    @php
                                        $val   = $this->getCellValue($row, $key);
                                        $color = $col['badge'][$val] ?? 'badge-neutral';
                                    @endphp
                                    <span class="badge {{ $color }} badge-sm font-medium">{{ $val }}</span>

                                @elseif(isset($col['render']))
                                    {!! $this->getCellValue($row, $key) !!}

                                @else
                                    {{-- Resaltado de términos buscados --}}
                                    @php
                                        $cellVal     = $this->getCellValue($row, $key);
                                        $activeTerms = array_filter(
                                            $filters,
                                            fn($f) => ($f['col'] ?? '') === $key && trim($f['value'] ?? '') !== ''
                                                   && in_array($f['operator'] ?? '', ['contains','starts_with','ends_with'])
                                        );
                                    @endphp

                                    @if(count($activeTerms))
                                        @php
                                            $highlighted = e($cellVal);
                                            foreach ($activeTerms as $af) {
                                                $term = e($af['value']);
                                                $highlighted = preg_replace(
                                                    '/('.preg_quote($term, '/').')/i',
                                                    '<mark class="bg-primary/20 text-primary rounded px-0.5">$1</mark>',
                                                    $highlighted
                                                );
                                            }
                                        @endphp
                                        <span class="truncate block max-w-xs">{!! $highlighted !!}</span>
                                    @else
                                        <span class="truncate block max-w-xs" title="{{ $cellVal }}">
                                            {{ $cellVal }}
                                        </span>
                                    @endif
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($this->visibleColumns()) }}" class="text-center py-16">
                            <div class="flex flex-col items-center gap-3 text-base-content/40">
                                <svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707L13 13.414V19a1 1 0 0 1-.553.894l-4 2A1 1 0 0 1 7 21v-7.586L3.293 6.707A1 1 0 0 1 3 6V4z"/>
                                </svg>
                                <p class="text-sm font-medium">Sin resultados</p>
                                <button wire:click="clearFilters" class="btn btn-ghost btn-xs">
                                    Limpiar filtros
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
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