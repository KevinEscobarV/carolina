<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Lotes
    </h2>
</x-slot>

<div class="container mx-auto sm:px-6 lg:px-8">
    <x-card title="Crear Lote">
        <x-slot name="action">
            <x-wireui-icon name="map" class="w-4 h-4 text-gray-500" />
        </x-slot>
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-6 gap-4">
            </div>
            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button type="submit" spinner="save" lime label="Guardar" />
            </div>
        </form>
    </x-card>
</div>