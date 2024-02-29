@props(['column', 'sortCol', 'sortAsc'])
<th {{ $attributes->merge(['class' => 'p-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-300']) }}>
    <button wire:click="sortBy('{{ $column }}')" class="flex items-center gap-2 group">
        {{ $slot }}

        @if ($sortCol === $column)
            <div class="text-gray-400">
                @if ($sortAsc)
                    <x-icon.arrow-long-up />
                @else
                    <x-icon.arrow-long-down />
                @endif
            </div>
        @else
            <div class="text-gray-400 opacity-0 group-hover:opacity-100">
                <x-icon.arrows-up-down />
            </div>
        @endif
    </button>
</th>
