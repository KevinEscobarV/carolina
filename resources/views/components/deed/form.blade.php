<div class="grid grid-cols-6 gap-6">
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                <span class="bg-clip-text font-bold text-2xl text-transparent bg-primary-500">
                    1
                </span>
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Selección de Promesa
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Promesa " wire:model="form.promise_id"
            placeholder="Buscar por Promesa o Usuario" :async-data="route('api.promises.index')" option-label="number"
            option-value="id" />
    </div>
    <div class="col-span-6">
        <div class="flex items-center gap-3 mt-6">
            <div class="border-2 border-lime-500 rounded-full h-10 w-10 flex items-center justify-center">
                <span class="bg-clip-text font-bold text-2xl text-transparent bg-lime-500">
                    2
                </span>
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Información de Escritura
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Numero de Escritura" right-icon="bookmark" placeholder="Numero Escritura" wire:model="form.number" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Libro (Opcional)" right-icon="book-open" placeholder="Numero de Libro" wire:model="form.book" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-inputs.currency label="Valor de Escritura" placeholder="Gastos de escrituración" right-icon="trending-up" prefix="$" thousands="," precision="0" wire:model="form.value" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Estado de Escritura" placeholder="Seleccione un estado" :options="App\Enums\DeedStatus::select()"
            option-label="label" option-value="value" wire:model="form.status" autocomplete="off" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-datetime-picker label="Fecha de escritura" placeholder="Fecha de escritura" wire:model="form.signature_date" without-time />
    </div>
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-yellow-500 rounded-full h-10 w-10 flex items-center justify-center">
                <span class="bg-clip-text font-bold text-2xl text-transparent bg-yellow-500">
                    4
                </span>
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Observaciones <span class="text-sm text-gray-500 dark:text-gray-400">(Opcional)</span>
            </h2>
        </div>
    </div>
    <div class="col-span-6">
        <x-wireui-textarea label="Observaciones" placeholder="Observaciones o concepto"
            wire:model="form.observations" />
    </div>
</div>