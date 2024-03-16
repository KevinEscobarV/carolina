<div class="border border-gray-300 dark:border-gray-600 rounded-xl flex flex-col gap-6 p-4">
    <div class="flex justify-between items-center">
        <div class="text-xl text-lime-500">
            Reporte general de pagos
        </div>
        <x-icon name="document-chart-bar" class="w-6 h-6 text-lime-500" />
    </div>
    <div class="flex flex-col gap-4">
        <x-wireui-datetime-picker label="Fecha Inicial" placeholder="Fecha de pago" wire:model="fromDate" without-time />
        <x-wireui-datetime-picker label="Fecha Final" placeholder="Fecha de pago" wire:model="toDate" without-time />
    </div>
    <div>
        <x-wireui-button class="w-full" icon="download" color="lime" label="Exportar" wire:click="exportGeneral" spinner="exportGeneral" />
    </div>
</div>
