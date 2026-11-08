<x-report.card icon="banknotes" title="Estado de cartera" description="Saldo y mora de cada promesa. Las fechas (de creación de la promesa) son opcionales.">
    <x-wireui-datetime-picker label="Desde" placeholder="Opcional" wire:model="fromDate" without-time />
    <x-wireui-datetime-picker label="Hasta" placeholder="Opcional" wire:model="toDate" without-time />

    <div class="col-span-2 flex items-center justify-between gap-3">
        <x-label for="onlyLate" value="Solo promesas en mora" />
        <x-wireui-toggle id="onlyLate" wire:model="onlyLate" lg />
    </div>
</x-report.card>
