<td class="py-3 px-3 text-sm sticky right-0 bg-gray-100 dark:bg-gray-700/50">
    <div class="flex gap-2">
        
        @can('edit.' . $model)
            @if (isset($route))
                <x-wireui-button.circle sm primary icon="pencil" href="{{ $route }}" />
            @else
                <x-wireui-button.circle sm primary icon="pencil" wire:click="edit({{ $item->id }})" />
            @endif
        @endcan

        @can('delete.' . $model)
            <x-wireui-button.circle sm negative icon="archive" wire:click="destroy({{ $item->id }})" />
        @endcan
    </div>
</td>
