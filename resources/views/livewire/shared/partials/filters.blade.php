<div class="flex flex-col gap-2">

    @foreach($filters as $i => $filter)

        @php
            $col = collect($columns)->firstWhere('key', $filter['col']);
            $type = $col->type ?? 'string';
        @endphp

        <div class="flex items-center gap-2">

            {{-- COLUMNA --}}
            <select wire:change="updateFilter({{ $i }}, 'col', $event.target.value)"
                    class="select select-sm">

                <option value="">Campo</option>

                @foreach($columns as $c)
                    <option value="{{ $c->key }}" @selected($filter['col'] === $c->key)>
                        {{ $c->label }}
                    </option>
                @endforeach

            </select>

            {{-- OPERADOR --}}
            <select wire:model.live="filters.{{ $i }}.operator"
                    class="select select-sm">

                @foreach($this->operators[$type] ?? [] as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach

            </select>

            {{-- INPUT DINÁMICO --}}
            @if($type === 'enum')

                <select wire:model.live="filters.{{ $i }}.value"
                        class="select select-sm">

                    <option value="">Seleccionar</option>

                    @foreach($col->options() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach

                </select>

            @elseif($type === 'numeric')

                <input type="number"
                    wire:model.live="filters.{{ $i }}.value"
                    class="input input-sm input-bordered"/>

            @else

                <input type="text"
                    wire:model.live="filters.{{ $i }}.value"
                    class="input input-sm input-bordered"
                    placeholder="Buscar..."/>

            @endif

            {{-- REMOVE --}}
            <button wire:click="removeFilter({{ $i }})" class="btn btn-sm btn-ghost">
                ✕
            </button>

        </div>

    @endforeach

</div>