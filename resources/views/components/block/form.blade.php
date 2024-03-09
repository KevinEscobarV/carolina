<div class="grid grid-cols-6 gap-6">
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-red-500 rounded-full h-10 w-10 flex items-center justify-center">
                <x-wireui-icon name="template" class="h-6 text-red-500" />
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Información de Manzana
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Campaña" wire:model="form.category_id"
            placeholder="Seleccione la campaña" :async-data="route('api.categories.index')" option-label="name"
            option-value="id" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Numero o Codigo de Manzana" right-icon="puzzle" placeholder="1" wire:model="form.code" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-inputs.number label="Area m²" min="0" step="0.01" placeholder="Metros Cuadrados" wire:model="form.area_m2"/>
    </div>
    <x-wireui-errors />
</div>