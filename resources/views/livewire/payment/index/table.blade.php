<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-3 p-6">
        <x-payment.header :$trash />
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
                    <x-table.th>
                        Lotes
                    </x-table.th>
                    <x-table.th>
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($payments as $payment)
                    <tr wire:key="{{ $payment->id }}" class="{{ $payment->is_initial_fee ? 'bg-primary-100 dark:bg-primary-700/30' : ''}}" title="{{ $payment->is_initial_fee ? 'Cuota Inicial' : ''}}">
                        <x-table.td class="bg-gray-400/10">
                            {{ $payment->id }}
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
                            @if ($payment->promise)
                                <div class="flex flex-col gap-1 max-h-20 soft-scrollbar overflow-auto">
                                    @forelse ($payment->promise->buyers as $user)
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $user->names }} {{ $user->surnames }}</span>
                                    @empty
                                        <span class="text-xs text-gray-600 dark:text-gray-400">Sin compradores</span>
                                    @endforelse
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Sin promesa</span>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if ($payment->promise)
                                {!! $payment->promise->parcels->groupBy('block_id')->map(function ($parcels) {
                                    return '<span class="dark:text-amber-500 text-amber-600 font-bold">' . $parcels->first()->block->code . ' : </span>' . $parcels->pluck('number')->join(', ');
                                })->join('<br>'); !!}
                            @else
                                <span class="text-xs text-gray-400">Sin promesa</span>
                            @endif
                        </x-table.td>
                        <x-table.actions :item="$payment" :route="route('payments.edit', $payment->id)" />
                    </tr>
                @empty
                    <tr>
                        <x-table.td colspan="12">
                            <div class="flex justify-center items-center gap-2">
                                <x-wireui-icon name="document-search" class="w-8 h-8 text-gray-400" />
                                <span class="font-medium py-8">No se encontraron pagos</span>
                            </div>
                        </x-table.td>
                    </tr>
                @endforelse
            </x-slot>
            <x-slot name="foot">
                <tr class="bg-gray-50 dark:bg-white/5">
                    <x-table.th colspan="3">
                        Resumen
                    </x-table.th>
                    <x-table.th class="text-right">
                        Valor Acordado
                    </x-table.th>
                    <x-table.th></x-table.th>
                    <x-table.th class="text-right">
                        Valor Pagado
                    </x-table.th>
                    <x-table.th colspan="8"></x-table.th>
                </tr>
                <tr>
                    <x-table.td colspan="3">
                        <div class="flex items center gap-2 py-8">
                            <span class="font-medium">Esta página</span>
                        </div>
                    </x-table.td>
                    <x-table.td>
                        <div class="flex flex-col items-end gap-1">
                            <p class="font-light text-md">
                                Suma
                            </p>
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ number_format($payments->sum('agreement_amount'), 0, ',', '.') }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </x-table.td>
                    <x-table.td></x-table.td>
                    <x-table.td>
                        <div class="flex flex-col items-end gap-1">
                            <p class="font-light text-md">
                                Suma
                            </p>
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ number_format($payments->sum('paid_amount'), 0, ',', '.') }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </x-table.td>
                </tr>
                <tr>
                    <x-table.td colspan="3">
                        <div class="flex items center gap-2 py-8">
                            <span class="font-medium">Todos los pagos</span>
                        </div>
                    </x-table.td>
                    <x-table.td>
                        <div class="flex flex-col items-end gap-1">
                            <p class="font-light text-md">
                                Suma
                            </p>
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $total_agreement_amount }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </x-table.td>
                    <x-table.td></x-table.td>
                    <x-table.td>
                        <div class="flex flex-col items-end gap-1">
                            <p class="font-light text-md">
                                Suma
                            </p>
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $total_paid_amount }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </x-table.td>
                </tr>
            </x-slot>
            @if ($payments->hasPages())
                <x-slot name="pagination">
                    {{ $payments->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
