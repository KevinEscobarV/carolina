<div class="border border-gray-300 dark:border-gray-600 rounded-xl flex flex-col gap-6 p-4">
    <div class="flex justify-between items-center">
        <div class="text-xl text-red-500">
            Reporte general de lotes
        </div>
        <x-icon name="map" class="w-6 h-6 text-red-500" />
    </div>
    <div class="grid grid-cols-2 gap-4">
        <x-wireui-select label="Filtrar posición" placeholder="Seleccione un posición" :options="App\Enums\ParcelPosition::select()"
            option-label="label" option-value="value" wire:model="position" autocomplete="off" />
        
        <x-wireui-select label="Filtrar estado" placeholder="Seleccione un estado" 
            :options="[['label' => 'Disponible 🟢', 'value' => 'available'], ['label' => 'Vendido 🟡', 'value' => 'sold']]"
            option-label="label" option-value="value" wire:model="status" autocomplete="off" />

        <x-wireui-select label="Filtrar folio" placeholder="Seleccione un item" 
            :options="[['label' => 'Con folio de matricula 🟢', 'value' => 'withRegistrationNumber'], ['label' => 'Sin folio de matricula 🟡', 'value' => 'withoutRegistrationNumber']]"
            option-label="label" option-value="value" wire:model="registrationNumber" autocomplete="off" />
    </div>
    <div class="mt-auto">
        <x-wireui-button class="w-full" icon="download" color="red" label="Exportar" wire:click="exportGeneral" spinner="exportGeneral" />
    </div>
</div>
