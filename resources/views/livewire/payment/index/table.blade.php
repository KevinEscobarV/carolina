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
                    <x-table.sortable column="agreement_date" :$sortCol :$sortAsc>
                        <div>Fecha Pactada</div>
                    </x-table.sortable>
                    <x-table.sortable column="amount" :$sortCol :$sortAsc>
                        <div>Valor</div>
                    </x-table.sortable>
                    <x-table.sortable column="payment_date" :$sortCol :$sortAsc>
                        <div>Fecha Pago</div>
                    </x-table.sortable>
                    <x-table.sortable column="paid_amount" :$sortCol :$sortAsc>
                        <div>Pagado</div>
                    </x-table.sortable>
                    <x-table.sortable column="payment_method" :$sortCol :$sortAsc>
                        <div>Valor Cancelado</div>
                    </x-table.sortable>
                    <x-table.sortable column="observations" :$sortCol :$sortAsc>
                        <div>Obsservaciones</div>
                    </x-table.sortable>
                    <x-table.th>
                        <div>Promesa</div>
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
                            {{ $payment->agreement_date->translatedFormat("F j/Y") }}
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->amount }}
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->payment_date->translatedFormat("F j/Y") }}
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->paid_amount }}
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->payment_method->label() }}
                        </x-table.td>
                        <x-table.td>
                            {{ str($payment->observations)->words(20) }}
                        </x-table.td>
                        <x-table.td>
                            {{ $payment->promise->promise }}
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
