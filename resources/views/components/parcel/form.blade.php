
<div class="grid grid-cols-6 gap-6">
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-lime-500 rounded-full h-10 w-10 flex items-center justify-center">
                <x-wireui-icon name="map" class="h-6 text-lime-500" />
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Información de Lote
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Manzana" wire:model="form.block_id"
            placeholder="Seleccione la Manzana" :async-data="route('api.blocks.index')" option-label="code"
            option-value="id" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Numero o Codigo de Lote" right-icon="puzzle" placeholder="1" wire:model="form.number" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Ubicación" placeholder="Seleccione un ubicación" :options="App\Enums\ParcelPosition::select()"
            option-label="label" option-value="value" wire:model="form.position" autocomplete="off" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-inputs.number label="Area m²" min="0" placeholder="Metros Cuadrados" wire:model="form.area_m2"/>
    </div>
    <div class="col-span-6 sm:col-span-4">
        <x-wireui-inputs.currency label="Valor Lote (Opcional)" placeholder="Valor Lote" right-icon="trending-up" prefix="$" thousands="," precision="0" wire:model="form.value" />
    </div>
    <div class="col-span-6">
        <div class="flex items-center gap-3 mt-6">
            <div class="border-2 border-yellow-500 rounded-full h-10 w-10 flex items-center justify-center">
                <x-wireui-icon name="scale" class="h-6 text-yellow-500" />
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Promesa <span class="text-sm text-gray-500 dark:text-gray-400">(Opcional)</span>
            </h2>
        </div>
    </div>
    
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Promesa" wire:model="form.promise_id"
            placeholder="Seleccione una promesa" :async-data="route('api.promises.index')" option-label="number"
            option-value="id" />
    </div>
</div>