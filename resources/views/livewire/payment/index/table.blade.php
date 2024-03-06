<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-3 p-6">
        <x-table.search />
        <x-wireui-select
            :options="[
                ['name' => '5 por página', 'id' => '5'],
                ['name' => '10 por página', 'id' => '10'],
                ['name' => '15 por página', 'id' => '15'],
                ['name' => '20 por página', 'id' => '20'],
                ['name' => '25 por página', 'id' => '25'],
                ['name' => '50 por página', 'id' => '50'],
                ['name' => '100 por página', 'id' => '100'],
            ]"
            option-label="name"
            option-value="id"
            wire:model.live="perPage"
            autocomplete="off"
        />
        <x-wireui-button x-on:click="open = ! open" lime>
            <x-wireui-icon name="plus" class="w-5 h-5" />
            <span class="ml-2" x-text="open ? 'Cerrar' : 'Crear Pago'"></span>
        </x-wireui-button>
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
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($payments as $payment)
                    <tr wire:key="{{ $payment->id }}">
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
                            <div class="flex flex-col gap-1 max-h-20 soft-scrollbar overflow-auto">
                                @forelse ($payment->promise->buyers as $uers)
                                    <span class="text-xs text-gray-400">{{ $uers->names }} {{ $uers->surnames }}</span>
                                @empty
                                    <span class="text-xs text-gray-400">Sin compradores</span>
                                @endforelse
                            </div>
                        </x-table.td>
                        <td class="whitespace-nowrap py-3 px-3 text-sm sticky right-0 bg-black/10">
                            <div class="flex gap-2">
                                <x-wireui-button.circle sm primary icon="pencil" href="{{ route('payments.edit', $payment->id) }}" wire:navigate />
                                <x-wireui-button.circle sm negative icon="trash" wire:click="archive({{ $payment->id }})" />
                            </div>
                        </td>                        
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
