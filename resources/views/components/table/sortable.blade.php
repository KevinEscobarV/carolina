@props(['column', 'sortCol', 'sortAsc', 'right' => false])
<th {{ $attributes->merge(['class' => 'py-3 px-6 text-sm font-semibold text-gray-900 dark:text-gray-300']) }}>
    <button wire:click="sortBy('{{ $column }}')" class="flex items-center gap-2 group w-full {{ $right ? 'justify-end' : 'justify-start' }}">
        
        @if (!$right)
            {{ $slot }}
        @endif

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

        @if ($right)
            {{ $slot }}
        @endif

    </button>
</th>
