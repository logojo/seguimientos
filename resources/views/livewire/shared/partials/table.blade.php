<?php
    use App\Support\DataTable\BadgeColumn;
    use App\Support\DataTable\ProgressColumn;
?>

<div class="overflow-x-auto">

    @include('livewire.shared.partials.loading')

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
                        @switch(true)

                            @case($col instanceof BadgeColumn)
                                <{!! $col->render($row) !!}
                            @break

                            @case($col instanceof ProgressColumn)
                                <progress class="progress w-full"
                                          value="{{ $row->{$col->key} }}"
                                          max="100"></progress>
                            @break

                            @default
                                {{ data_get($row, $col->key) }}

                        @endswitch
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