<div class="border border-base-300 rounded-box overflow-auto">

    <table class="table table-sm w-full">

        <thead class="bg-base-200/50">
        <tr>
            @foreach($this->visibleColumns() as $col)
                <th wire:click="sort('{{ $col->key }}')" class="cursor-pointer">
                    <div class="inline-flex items-center gap-1.5">
                        <span>{{ $col->label }}</span> 

                        @if( $col->sortable )
                            <span class="material-symbols-outlined opacity-40" style="font-size: 14px">
                                @if($sortColumn === $col->key)
                                    {{ $sortDirection === 'asc' ? 'straight' : 'south' }}
                                @else mobiledata_arrows @endif
                            </span>
                        @endif
                    </div>
                </th>
            @endforeach
        </tr>
        </thead>

        <tbody>
        @forelse($this->rows as $row)
            <tr class="hover:bg-base-100">
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