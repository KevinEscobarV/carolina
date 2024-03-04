<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.search />
        {{-- <x-payment.index.bulk-actions /> --}}
    </div>
    {{-- payments table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.sortable column="id" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">ID</div>
                    </x-table.sortable>
                    <x-table.sortable column="bill_number" :$sortCol :$sortAsc>
                        Numero Recibo
                    </x-table.sortable>
                    <x-table.sortable column="agreement_date" :$sortCol :$sortAsc>
                        Fecha Pactada
                    </x-table.sortable>
                    <x-table.sortable column="agreement_amount" :$sortCol :$sortAsc right>
                        Valor Acordado
                    </x-table.sortable>
                    <x-table.sortable column="payment_date" :$sortCol :$sortAsc>
                        Fecha Pago
                    </x-table.sortable>
                    <x-table.sortable column="paid_amount" :$sortCol :$sortAsc right>
                        Valor Pagado
                    </x-table.sortable>
                    <x-table.sortable column="bank" :$sortCol :$sortAsc>
                        Banco
                    </x-table.sortable>
                    <x-table.sortable column="payment_method" :$sortCol :$sortAsc>
                        Metodo Pago
                    </x-table.sortable>
                    <x-table.sortable column="observations" :$sortCol :$sortAsc>
                        Obsservaciones
                    </x-table.sortable>
                    <x-table.th>
                        Promesa
                    </x-table.th>
                    <x-table.th>
                        Comprador
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($payments as $payment)
                    <tr wire:key="{{ $payment->id }}">
                        <x-table.td>
                            <div class="flex gap-1">
                                <span class="text-gray-300">#</span>
                                {{ $payment->id }}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->bill_number }}
                        </x-table.td>
                        <x-table.td class="first-letter:uppercase">
                            {{ $payment->agreement_date ? $payment->agreement_date->translatedFormat("F j/Y") : 'Sin definir' }}
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $payment->agreement_amount_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td class="first-letter:uppercase">
                            {{ $payment->payment_date ? $payment->payment_date->translatedFormat("F j/Y") : 'Sin definir' }}
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $payment->paid_amount_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->bank ? $payment->bank : '💰' }}
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->payment_method->label() }}
                        </x-table.td>
                        <x-table.td>
                            {{ str($payment->observations)->words(20) }}
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->promise ? $payment->promise->number : 'Sin promesa' }}
                        </x-table.td>
                        <x-table.td>
                            <div class="flex flex-col gap-1 max-h-20 soft-scrollbar overflow-auto">
                                @forelse ($payment->promise->buyers as $uers)
                                    <span class="text-xs text-gray-400">{{ $uers->names }} {{ $uers->surnames }}</span>
                                @empty
                                    <span class="text-xs text-gray-400">Sin compradores</span>
                                @endforelse
                            </div>
                        </x-table.td>
                    </tr>
                @empty
                    <tr>
                        <x-table.td colspan="8">
                            <div class="flex justify-center items-center gap-2">
                                <x-wireui-icon name="document-search" class="w-8 h-8 text-gray-400" />
                                <span class="font-medium py-8">No se encontraron pagos</span>
                            </div>
                        </x-table.td>
                    </tr>
                @endforelse
            </x-slot>
            @if ($payments->hasPages())
                <x-slot name="pagination">
                    {{ $payments->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
