<div class="flex gap-2 flex-wrap">

    @foreach($this->externalFiltersConfig() as $key => $filter)
        
        @if($filter['type'] === 'select')
            <div>
                <select 
                  class="select select-sm min-w-xs" 
                   wire:change="setExternalFilter('{{ $key }}', $event.target.value)"
                >                    
                    @foreach($this->getFilterOptions($key) as $value => $label)
                        <option value="{{ $value }}"
                            @selected(($externalFilters[$key] ?? '') == $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

    @endforeach

</div>