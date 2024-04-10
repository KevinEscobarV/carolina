<div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10 overflow-hidden">
    <div class="overflow-x-auto soft-scrollbar">
        {{-- payments table... --}}
        <table class="min-w-full table-fixed text-gray-800">
            <thead class="bg-gray-50 dark:bg-white/5 text-left border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <x-table.th>
                        Cuota
                    </x-table.th>
                    <x-table.th>
                        Fecha de Pago
                    </x-table.th>
                    <x-table.th>
                        Valor Cuota
                    </x-table.th>
                    <x-table.th>
                        Saldo
                    </x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700 dark:divide-gray-700 dark:text-gray-300">
                @forelse ($projection as $key => $item)
                    <tr>
                        <x-table.td>
                            {{ $item['number'] }}
                        </x-table.td>
                        
                        <x-table.td>
                            <x-wireui-input type="date" wire:model="form.projection.{{ $key }}.due_date" class="w-full" />
                        </x-table.td>

                        <x-table.td class="text-right font-light whitespace-nowrap">
                            <x-wireui-inputs.currency placeholder="Valor Cuota" right-icon="trending-down" prefix="$" thousands="," precision="0" wire:model="form.projection.{{ $key }}.payment_amount" />
                        </x-table.td>

                        <x-table.td class="text-right font-light">
                            <span class="text-gray-400">$</span> {{ number_format($item['remaining_balance'], 0, ',', '.') }}
                        </x-table.td>
                    </tr>
                @empty
                    <tr>
                        <x-table.td colspan="6" class="text-center">
                            No hay proyección de pagos
                        </x-table.td>
                    </tr>
                @endforelse
                <tr class="bg-gray-50 dark:bg-white/5">
                    <x-table.td colspan="6" class="text-center">
                        <x-icon name="information-circle" class="w-6 h-6 text-orange-400 inline-flex" />
                        Puedes editar la fecha de pago y el valor de la cuota si la tasa de interés es 0
                    </x-table.td>
                </tr>
            </tbody>
        </table>
    </div>
</div>