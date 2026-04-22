@props([
    'title',
    'lgSize' => 'lg:text-3xl',
])

<div class="py-5">
    <h2 class="text-xl md:text-2xl {{ $lgSize }} font-patria font-semibold">{{ $title }}</h2>
    <div class="w-full border-t border-t-gray-300 mt-3"></div>
    <div class="h-1.5 bg-secondary w-10"></div>
</div>