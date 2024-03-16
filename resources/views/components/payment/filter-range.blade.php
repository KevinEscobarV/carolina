@props(['filters'])

<div>
    <x-wireui-dropdown persistent width="min-w-full">
        <x-slot name="trigger">
            <x-wireui-button label="{{ $filters->range->label($filters->start, $filters->end) }}" gray />
        </x-slot>
     
        @foreach (\App\Enums\Range::cases() as $range)
            @if ($range === \App\Enums\Range::Custom)
                <div x-data="{ showCustomRangePanel: $wire.filters.range === '{{ \App\Enums\Range::Custom }}' ? true : false }">

                    <x-wireui-dropdown.item label="Personalizado" x-on:click="showCustomRangePanel = ! showCustomRangePanel" icon="chevron-down" />

                    <form
                        x-show="showCustomRangePanel"
                        x-collapse class="flex flex-col pt-3 gap-3 p-2"
                        wire:submit="$set('filters.range', '{{ \App\Enums\Range::Custom }}')"
                        x-on:submit="$popover.close()"
                    >
                        <div class="flex justify-between items-center gap-2">
                            <x-wireui-input type="date" wire:model="filters.start" without-time required />

                            <span class="text-sm text-gray-700">&</span>

                            <x-wireui-input type="date" wire:model="filters.end" without-time required />
                        </div>

                        <div class="flex">
                            <x-wireui-button label="Aplicar" type="submit" primary />
                        </div>
                    </form>
                </div>
            @else 
                <x-wireui-dropdown.item icon="{{ $range === $filters->range ? 'check' : '' }}" wire:click="$set('filters.range', '{{ $range }}')">
                    <x-slot name="label">
                        <div class="text-sm">{{ $range->label() }}</div>
                    </x-slot>
                </x-wireui-dropdown.item>
            @endif
        @endforeach
    </x-wireui-dropdown>
</div>
