@props([
        'button', 
        'title', 
        'icon', 
        'size' => 'max-w-4xl',
        'mini' => false, 
        'btnColor' => 
        'btn-primary', 
        'btnType' => '', 
        'hasButton' => true,
])


<div 
 x-data="{ open: @entangle('open').live }"
 @close-modal.window="open = false"  
>

@if ($hasButton)
    @if( $mini == 1 )
    <span class="material-symbols-outlined text-secondary cursor-pointer" x-on:click="open = true">{{ $button }}</span>
    @else
        <button class="btn {{ $btnType }} {{$btnColor}}" x-on:click="open = true" type="button" wire:click="$dispatch('clear-form')">
            <span class="material-symbols-outlined">{{ $icon }}</span>
            {{ $button }}
        </button>
    @endif
@endif

<dialog x-ref="modal" class="modal" :open="open">
    <div class="modal-box w-11/12 {{ $size }} bg-white">
        <h3 class="text-xl md:text-2xl font-bold text-primary">{{ $title }}</h3>
        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" type="button" x-on:click="open = false">
        <span class="material-symbols-outlined">close</span>
        </button>

        <section>
           <div>
             {{ $content }}
           </div>
           <div class="modal-action">
               {{ $footer }}
           </div>
        </section>
    </div>
</dialog>

</div>

