<div class="flex gap-2 flex-wrap">
    @foreach($filters as $i => $filter)
        <div class="badge badge-info gap-2">
            {{ $filter['col'] }}: {{ $filter['value'] }}
            <button wire:click="removeFilter({{ $i }})">✕</button>
        </div>
    @endforeach
</div>