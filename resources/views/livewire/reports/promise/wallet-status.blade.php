<x-report.card icon="banknotes" title="Estado de cartera" description="Cada promesa activa con su último pago, saldo, cuota pendiente y días de mora a la fecha de corte. Incluye una hoja de resumen.">
    <div class="col-span-2">
        <x-wireui-datetime-picker label="Fecha de corte" wire:model="cutOffDate" without-time without-timezone
            parse-format="YYYY-MM-DD" display-format="DD/MM/YYYY" :clearable="false" />
    </div>

    <div class="col-span-2 flex items-center justify-between gap-3">
        <x-label for="onlyLate" value="Solo promesas en mora" />
        <x-wireui-toggle id="onlyLate" wire:model="onlyLate" lg />
    </div>
</x-report.card>
