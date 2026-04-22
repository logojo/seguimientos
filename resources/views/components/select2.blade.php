@props([
    'disabled' => false,
    'config' => '{}',
])

<div 
    wire:ignore
    x-data="{
        tom: null,
        initTomSelect() {

            this.tom = new TomSelect($refs.select, JSON.parse(this.config || '{}'));

            const model = '{{ $attributes->wire('model')->value() }}';
            const currentValue = $wire.get(model);
            if (currentValue) {
                this.tom.setValue(currentValue);
            }

            this.tom.on('change', (value) => {
                $wire.set(model, value);
            });
        },
        refreshTomSelect() {
            // Espera a que Livewire actualice el DOM
            $nextTick(() => {
                this.initTomSelect();
            });
        }
    }"
    x-init="initTomSelect()"
    
>
    <select 
        x-ref="select"
        {{ $disabled ? 'disabled' : '' }}
        {!! $attributes->merge(['class' => 'input-select']) !!}
    >
        {{ $slot }}
    </select>
</div>
