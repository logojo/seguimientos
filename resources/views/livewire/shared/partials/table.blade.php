<div class="overflow-x-auto">

    <table class="table table-zebra w-full">

        <thead>
        <tr>
            @foreach($this->visibleColumns() as $col)
                <th wire:click="sort('{{ $col->key }}')" class="cursor-pointer">
                    {{ $col->label }}
                </th>
            @endforeach
        </tr>
        </thead>

        <tbody>
        @forelse($this->rows as $row)
            <tr>
                @foreach($this->visibleColumns() as $col)
                    <td>
                        {!! $col->render($row) !!}
                    </td>
                @endforeach
            </tr>
        @empty
            @include('livewire.shared.partials.empty')
        @endforelse
        </tbody>

    </table>

    <div class="mt-3">
        {{ $this->rows->links() }}
    </div>

</div>