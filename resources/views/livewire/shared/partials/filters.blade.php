<div class="flex flex-col gap-2">

    @foreach($filters as $i => $filter)

        @php
            $col = collect($columns)->firstWhere('key', $filter['col']);
            $type = $col->type ?? 'string';
        @endphp

        <div class="flex items-center gap-2 flex-wrap bg-base-200/50 border border-base-300 rounded-xl px-3 py-2">

            {{-- COLUMNA --}}
            <select wire:change="updateFilter({{ $i }}, 'col', $event.target.value)"
                    class="select select-bordered select-sm">                

                @foreach($columns as $c)    
                    @if ( $c->key === 'actions' )
                        @continue
                    @endif           
                    <option value="{{ $c->key }}" @selected($filter['col'] === $c->key)>
                        {{ $c->label == '' ? 'Seguimiento' : $c->label}}
                    </option>
                @endforeach

            </select>

            {{-- OPERADOR --}}
            <select wire:model.live="filters.{{ $i }}.operator"
                    class="select select-bordered select-sm">

                @foreach($this->operators[$type] ?? [] as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach

            </select>

            {{-- INPUT DINÁMICO --}}
            @if($type === 'enum')

                <select wire:model.live="filters.{{ $i }}.value"
                        class="select select-bordered select-sm flex-1 min-w-32">

                    <option value="">Seleccionar</option>

                    @foreach($col->options() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach

                </select>
            @elseif($type === 'icon')

                <select wire:model.live="filters.{{ $i }}.value"
                        class="select select-bordered select-sm flex-1 min-w-32">

                    <option value="">Seleccionar</option>
                    <option value="edit">Editada</option>
                    <option value="no-edit">No editada</option>
                  

                </select>

            @elseif($type === 'numeric')

                <input type="number"
                    wire:model.live="filters.{{ $i }}.value"
                    class="input input-bordered input-sm flex-1 min-w-32"/>

            @else

                <input type="text"
                    wire:model.live="filters.{{ $i }}.value"
                    class="input input-bordered input-sm flex-1 min-w-32"
                    placeholder="Buscar..."/>

            @endif

            {{-- REMOVE --}}
            <button wire:click="removeFilter({{ $i }})" class="btn btn-ghost btn-sm btn-square text-base-content/40 hover:text-primary">
                ✕
            </button>

        </div>

    @endforeach

</div>