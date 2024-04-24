<div class="col-span-3 flex flex-row-reverse justify-end sm:justify-start sm:flex-row gap-2">
    <div class="flex items-center gap-1 text-sm text-gray-600">
        <span x-text="$wire.selectedIds.length"></span>

        <span>Seleccionados</span>
    </div>

    <div class="flex items-center px-3">
        <div class="h-[75%] w-[1px] bg-gray-300"></div>
    </div>
    <form wire:submit="sendSelected">
        <x-wireui-button type="submit" class="h-full" icon="speakerphone" label="Enviar Mensajes" lime spinner="sendSelected" />
    </form>
    <x-wireui-button wire:click="selectAll" class="h-full" icon="check" label="Seleccionar Todas las Paginas" indigo spinner="selectAll" />
</div>
