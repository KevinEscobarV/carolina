<div class="col-span-3">
    <x-wireui-input wire:model.live.debounce="search" type="text" icon="search" placeholder="Buscar ..." />
</div>
<div class="col-span-1">
    <x-wireui-select :options="[
        ['name' => '5 por página', 'id' => '5'],
        ['name' => '10 por página', 'id' => '10'],
        ['name' => '15 por página', 'id' => '15'],
        ['name' => '20 por página', 'id' => '20'],
        ['name' => '25 por página', 'id' => '25'],
        ['name' => '50 por página', 'id' => '50'],
        ['name' => '100 por página', 'id' => '100'],
    ]" option-label="name" option-value="id" wire:model.live="perPage" autocomplete="off" />
</div>
<div class="col-span-1">
    @if (isset($routeCreate))
        <x-wireui-button href="{{ $routeCreate }}" class="h-full" icon="plus" label="Crear" lime />
    @else
        <x-wireui-button x-on:click="open = ! open" class="h-full" lime>
            <span class="mr-2" x-text="open ? 'Cerrar' : 'Crear'"></span>
            <x-wireui-icon name="plus" class="w-5 h-5" />
        </x-wireui-button>
    @endif
</div>

<div class="col-span-3 flex justify-end gap-3">
    @if (isset($import))
        {{ $import }}
    @endif
    @if (isset($trash))
        <x-wireui-button icon="{{ $trash ? 'x' : 'trash' }}" 
            label="{{ $trash ? 'Cerrar Papelera' : 'Papelera' }}" 
            color="{{ $trash ? 'secondary' : 'negative' }}"
            spinner="onlyTrash"
            wire:click="onlyTrash"
        />
    @endif
</div>
