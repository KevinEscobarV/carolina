<td class="whitespace-nowrap py-3 px-3 text-sm sticky right-0 bg-black/5">
    <div class="flex gap-2">
        <x-wireui-button.circle sm primary icon="pencil" wire:click="edit({{ $item->id }})" spinner="edit({{ $item->id }})" />
        <x-wireui-button.circle sm negative icon="trash"
            x-on:confirm="{
                title: 'Eliminar Pago',
                icon: 'warning',
                method: 'delete',
                acceptLabel: 'Eliminar',
                rejectLabel: 'Cancelar',
                params: { id: {{ $item->id }} }
            }"
        />
    </div>
</td>
