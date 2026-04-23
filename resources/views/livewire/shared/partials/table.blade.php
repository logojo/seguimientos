<?php
    use App\Support\DataTable\BadgeColumn;
    use App\Support\DataTable\ProgressColumn;
?>

<div class="overflow-x-auto">

    <table class="table table-zebra w-full">

        <thead>
        <tr>
            @foreach($this->columnsDef() as $col)
                <th wire:click="sort('{{ $col->key }}')" class="cursor-pointer">
                    {{ $col->label }}
                </th>
            @endforeach
        </tr>
        </thead>

        <tbody>
        @forelse($this->rows as $row)

            <tr>
                @foreach($this->columnsDef() as $col)
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

    {{ $this->rows->links() }}

</div>