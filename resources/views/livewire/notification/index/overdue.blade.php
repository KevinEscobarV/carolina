<div class="col-span-12">
    <div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
        <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
            <x-notification.bulk-actions />
        </div>
        <x-table.template>
                <x-slot name="head">
                    <tr>
                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <div class="flex items-center">
                                <x-notification.check-all />
                            </div>
                        </th>
                        <x-table.sortable column="number" :$sortCol :$sortAsc>
                            Promesa
                        </x-table.sortable>
                        <x-table.sortable column="buyer" :$sortCol :$sortAsc>
                            Comprador
                        </x-table.sortable>
                        <x-table.th>
                            Cuota vencida
                        </x-table.th>
                        <x-table.th>
                            Días de mora
                        </x-table.th>
                        <x-table.th class="text-right">
                            Valor en mora
                        </x-table.th>
                        <x-table.sortable column="value" :$sortCol :$sortAsc right>
                            Valor Promesa
                        </x-table.sortable>
                        <x-table.sortable column="initial_fee" :$sortCol :$sortAsc right>
                            Cuota Inicial
                        </x-table.sortable>
                        <x-table.sortable column="quota_amount" :$sortCol :$sortAsc>
                            Valor Cuota
                        </x-table.sortable>
                        <x-table.th>
                            Total Pagado
                        </x-table.th>
                        <x-table.th>
                            Numero Cuotas
                        </x-table.th>
                        <x-table.th>
                            Cuotas Pagadas
                        </x-table.th>
                        <x-table.sortable column="payment_frequency" :$sortCol :$sortAsc>
                            Periodicidad
                        </x-table.sortable>
                        <x-table.sortable column="interest_rate" :$sortCol :$sortAsc>
                            Interes
                        </x-table.sortable>
                        <x-table.sortable column="payment_method" :$sortCol :$sortAsc>
                            Metodo Pago
                        </x-table.sortable>
                        <x-table.th>
                            Lotes
                        </x-table.th>
                    </tr>
                </x-slot>
                <x-slot name="body">
                    @forelse ($promises as $promise)
                        <tr wire:key="{{ $promise->id }}">
                            <td class="whitespace-nowrap p-3 text-sm">
                                <div class="flex items-center">
                                    <input wire:model="selectedIds" value="{{ $promise->id }}" type="checkbox" class="rounded border-gray-300 shadow">
                                </div>
                            </td>
                            <x-table.td>
                                {{ $promise->number }}
                            </x-table.td>
                            <x-table.td>
                                <div class="flex flex-col gap-1 max-h-20 soft-scrollbar overflow-auto">
                                    @forelse ($promise->buyers as $user)
                                        <span class="text-xs text-gray-600 dark:text-gray-300">{{ $user->names }} {{ $user->surnames }}</span>
                                    @empty
                                        <span class="text-xs text-gray-600 dark:text-gray-300">Sin compradores</span>
                                    @endforelse
                                </div>
                            </x-table.td>
                            @php($standing = $promise->standing())
                            <x-table.td>
                                @if ($standing->currentQuota)
                                    {{ Carbon\Carbon::parse($standing->currentQuota['due_date'])->format('d/m/Y') }}
                                    <span class="text-xs text-gray-400">(cuota {{ $standing->currentQuota['number'] }})</span>
                                @else
                                    <span class="text-xs text-gray-400">Al día</span>
                                @endif
                            </x-table.td>
                            <x-table.td class="bg-red-500/20 dark:bg-red-500/50">
                                {{ $standing->daysOverdue }}
                            </x-table.td>
                            <x-table.td class="text-right font-medium text-red-600 dark:text-red-400">
                                <span class="text-gray-400">$</span> {{ number_format($standing->overdueAmount, 0, ',', '.') }}
                            </x-table.td>
                            <x-table.td class="text-right">
                                <p class="font-light text-lg">
                                    <span class="text-gray-400">$</span> {{ $promise->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                                </p>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <div class="relative">
                                    <div class="font-light text-lg">
                                        <span class="text-gray-400">$</span> {{ $promise->initial_fee_formatted }} <span class="text-gray-400 text-sm">COP</span>
                                    </div>
                                    <div class="absolute -top-1 -right-2" x-show="!{{ $promise->has_initial_fee }}">
                                        <div class="w-2 h-2 bg-amber-500 rounded-full dark:bg-amber-400" title="Sin registrar">
                                            <div class="w-2 h-2 bg-amber-500 rounded-full dark:bg-amber-400 animate-ping">
                                        </div>
                                    </div>
                                </div>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <p class="font-light text-lg">
                                    <span class="text-gray-400">$</span> {{ $promise->quota_amount_formatted }} <span class="text-gray-400 text-sm">COP</span>
                                </p>
                            </x-table.td>
                            <x-table.td class="text-right bg-lime-500/10">
                                <p class="font-light text-lg">
                                    <span class="text-gray-400">$</span> {{ $promise->total_paid_formatted }} <span class="text-gray-400 text-sm">COP</span>
                                </p>
                            </x-table.td>
                            <x-table.td>
                                {{ $promise->number_of_fees }}
                            </x-table.td>
                            <x-table.td class="bg-lime-500/10">
                                {{ $promise->number_of_paid_fees }}
                            </x-table.td>
                            <x-table.td>
                                {{ $promise->payment_frequency->label() }}
                            </x-table.td>
                            <x-table.td>
                                {{ $promise->interest_rate }}%
                            </x-table.td>
                            <x-table.td>
                                {{ $promise->payment_method->label() }}
                            </x-table.td>
                            <x-table.td>
                                @if ($promise->parcels->isNotEmpty())
                                    {!! $promise->parcels->groupBy('block_id')->map(function ($parcels) {
                                        return '<span class="dark:text-amber-500 text-amber-600 font-bold">' . $parcels->first()->block->code . ' : </span>' . $parcels->pluck('number')->join(', ');
                                    })->join('<br>'); !!}
                                @else
                                    <span class="text-xs text-gray-400">Sin lotes</span>
                                @endif
                            </x-table.td>
                        </tr>
                    @empty
                        <tr>
                            <x-table.td colspan="16">
                                <div class="flex justify-center items-center gap-2">
                                    <x-wireui-icon name="document-search" class="w-8 h-8 text-gray-400" />
                                    <span class="font-medium py-8">No se encontraron registros</span>
                                </div>
                            </x-table.td>
                        </tr>
                    @endforelse
                </x-slot>
                @if ($promises->hasPages())
                    <x-slot name="pagination">
                        {{ $promises->links() }}
                    </x-slot>
                @endif
        </x-table.template>
    </div>    
</div>