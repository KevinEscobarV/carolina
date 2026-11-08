<x-report.card icon="presentation-chart-bar" title="Pagos" description="Los pagos hechos entre estas fechas.">
    <x-wireui-datetime-picker label="Desde" placeholder="Fecha de pago" wire:model="fromDate" without-time />
    <x-wireui-datetime-picker label="Hasta" placeholder="Fecha de pago" wire:model="toDate" without-time />

    <div class="col-span-2">
        <x-wireui-select label="Medios de pago" placeholder="Todos" :options="App\Enums\PaymentMethod::select()"
            option-label="label" option-value="value" wire:model="paymentMethods" autocomplete="off" multiselect />
    </div>
</x-report.card>
