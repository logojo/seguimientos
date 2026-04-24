<div class="border border-base-300 rounded-box overflow-auto">

    <table class="table table-zebra table-sm w-full">

        <thead class="bg-base-200/50">
        <tr>
            @foreach($this->visibleColumns() as $col)
                <th wire:click="sort('{{ $col->key }}')" class="cursor-pointer">
                    <div class="inline-flex items-center gap-1.5">
                        <span>{{ $col->label }}</span> 
                        <span class="material-symbols-outlined opacity-40" style="font-size: 14px">mobiledata_arrows</span>
                    </div>
                </th>
            @endforeach
        </tr>
        </thead>

        <tbody>
        @forelse($this->rows as $row)
            <tr class="hover">
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

    <div class="p-3">
        {{ $this->rows->links() }}
    </div>

</div>