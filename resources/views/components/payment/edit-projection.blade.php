<div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10 overflow-hidden">
    <div class="overflow-x-auto soft-scrollbar">
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
                        Opciones
                    </x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700 dark:divide-gray-700 dark:text-gray-300">
                @forelse ($projection as $key => $item)
                    <tr class="{{ isset($item['is_last']) ? '!bg-red-100 dark:!bg-red-900/20' : '' }} {{ isset($item['payment']) ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                        <x-table.td>
                            {{ $item['number'] }}
                        </x-table.td>
                        
                        <x-table.td>
                            <x-wireui-input type="date" wire:model="projection.{{ $key }}.due_date" class="w-full" />
                        </x-table.td>

                        <x-table.td class="text-left font-light whitespace-nowrap">
                            <x-wireui-inputs.currency placeholder="Valor Cuota" right-icon="trending-down" prefix="$" thousands="," precision="0" wire:model="projection.{{ $key }}.payment_amount" />
                        </x-table.td>
                        <x-table.td class="flex justify-end items-center gap-2">
                            <x-wireui-button.circle wire:click="deleteQuota({{ $key }})" type="button" icon="x" size="sm" red :disabled="isset($item['payment']) ? true : false" />
                        </x-table.td>
                    </tr>
                    @if (isset($item['payment']))
                        <tr class="bg-primary-50 dark:bg-primary-900/20">
                            <x-table.td>
                                {{ $item['payment']['bill_number'] }}
                            </x-table.td>
                            <x-table.td class="first-letter:uppercase">
                                {{ $item['payment']['payment_date_formatted'] }}
                            </x-table.td>
                            <x-table.td>
                                <p class="font-light text-lg">
                                    <span class="text-gray-400">$</span> {{ $item['payment']['paid_amount_formatted'] }} <span class="text-gray-400 text-sm">COP</span>
                                </p>
                            </x-table.td>
                            <x-table.td class="flex justify-end items-center gap-2">
                                <x-wireui-button.circle wire:click="infoPayment({{ $item['payment']['id'] }})" type="button" icon="information-circle" outline size="sm" indigo />
                            </x-table.td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <x-table.td colspan="6" class="text-center">
                            No hay proyección de pagos
                        </x-table.td>
                    </tr>
                @endforelse
                <tr class="bg-gray-50 dark:bg-white/5">
                    <x-table.td colspan="6">
                        <x-wireui-button class="w-full" rounded wire:click="addQuota" type="button" icon="plus" label="Agregar Quota" size="sm" color="green" spinner="addQuota" />
                    </x-table.td>
                </tr>
            </tbody>
        </table>
    </div>
</div>