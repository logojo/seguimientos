<div class="flex flex-wrap justify-between items-center gap-2">

    {{-- IZQUIERDA: ACCIONES --}}
    <div class="flex items-center gap-2">

        @include('livewire.shared.partials.external-filters')

        {{-- Agregar filtro --}}
        <button wire:click="addFilter" class="btn btn-sm btn-outline gap-2">
            + Añadir filtro
            @if( count($filters) > 0 )
            <span class="badge badge-primary badge-sm">{{ count($filters) }}</span>
            @endif
            
        </button>

        {{-- Limpiar filtros --}}
        @if(count($filters))
            <button wire:click="clearFilters" class="btn btn-sm btn-primary">
                Limpiar
            </button>
        @endif

       @include('livewire.shared.partials.loading')

    </div>

    {{-- DERECHA: CONFIGURACIÓN --}}
    <div class="flex items-center gap-2">

        {{-- Columnas --}}
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-sm">
                <span class="material-symbols-outlined" style="font-size: 15px">grid_view</span>
                Columnas
            </label>

            <ul class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52">
                @foreach($columns as $col)
                    <li>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                   class="checkbox checkbox-xs"
                                   wire:click="toggleColumn('{{ $col->key }}')"
                                   @checked(!in_array($col->key, $hiddenColumns))
                            >
                            {{ $col->label === '' ? 'Seguimiento' : $col->label  }}
                        </label>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Per page --}}
        <select wire:model.live="perPage" class="select select-sm">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>

    </div>

</div>