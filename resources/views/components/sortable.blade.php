@props(['column', 'sortCol' => null, 'sortAsc'])

<button wire:click="sortBy('{{ $column }}')" class="flex items-center gap-2 group">
    {{ $slot }}
    @if ($sortCol == $column)
        @if ($sortAsc)
            <x-wireui-icon name="arrow-up" class="w-4 h-4 text-gray-500" />
        @else
            <x-wireui-icon name="arrow-down" class="w-4 h-4 text-gray-500" />
        @endif
    @else
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500 group-hover:text-gray-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
        </svg>          
    @endif
</button>