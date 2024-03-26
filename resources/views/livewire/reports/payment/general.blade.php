<div class="border border-gray-300 dark:border-gray-600 rounded-xl flex flex-col gap-6 p-4">
    <div class="flex justify-between items-center">
        <div class="text-xl text-green-500">
            Reporte general de pagos
        </div>
        <x-icon name="presentation-chart-bar" class="w-6 h-6 text-green-500" />
    </div>
    <div class="grid grid-cols-2 gap-4">
        <x-wireui-datetime-picker label="Fecha Inicial" placeholder="Fecha de pago" wire:model="fromDate" without-time />
        <x-wireui-datetime-picker label="Fecha Final" placeholder="Fecha de pago" wire:model="toDate" without-time />
        <div class="col-span-2 flex justify-center items-end gap-3 -mt-1">
            <x-wireui-icon name="reply" class="w-5 h-5 text-gray-500 -scale-x-100 -rotate-90" />
            <p class="text-sm text-gray-500 -mb-1">
                Fecha de pago
            </p>
            <x-wireui-icon name="reply" class="w-5 h-5 text-gray-500 rotate-90" />
        </div>
        <x-wireui-select label="Filtrar Medio de Pago" placeholder="Seleccione un medios de pago" :options="App\Enums\PaymentMethod::select()"
            option-label="label" option-value="value" wire:model="paymentMethods" autocomplete="off" multiselect />
    </div>
    <div>
        <x-wireui-button class="w-full" icon="download" color="green" label="Exportar" wire:click="exportGeneral" spinner="exportGeneral" />
    </div>
</div>
