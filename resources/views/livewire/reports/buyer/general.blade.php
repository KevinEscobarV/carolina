<x-report.card icon="users" title="Clientes compradores" description="Los clientes creados entre estas fechas.">
    <x-wireui-datetime-picker label="Desde" placeholder="Fecha de creación" wire:model="fromDate" without-time display-format="DD/MM/YYYY" />
    <x-wireui-datetime-picker label="Hasta" placeholder="Fecha de creación" wire:model="toDate" without-time display-format="DD/MM/YYYY" />
</x-report.card>
