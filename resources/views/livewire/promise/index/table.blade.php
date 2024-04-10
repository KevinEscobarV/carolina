<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.header :$trash routeCreate="{{route('promises.create')}}" />
        {{-- <x-promise.index.bulk-actions /> --}}
    </div>
    {{-- promises table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.sortable column="id" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">ID</div>
                    </x-table.sortable>
                    <x-table.sortable column="number" :$sortCol :$sortAsc>
                        Promesa
                    </x-table.sortable>
                    <x-table.sortable column="buyer" :$sortCol :$sortAsc>
                        Comprador
                    </x-table.sortable>
                    <x-table.sortable column="status" :$sortCol :$sortAsc>
                        Estado
                    </x-table.sortable>
                    <x-table.sortable column="signature_date" :$sortCol :$sortAsc>
                        Fecha Firma
                    </x-table.sortable>
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
                    <x-table.sortable column="observations" :$sortCol :$sortAsc>
                        Obsservaciones
                    </x-table.sortable>
                    <x-table.th>
                        Lotes
                    </x-table.th>
                    <x-table.th>
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($promises as $promise)
                    <tr wire:key="{{ $promise->id }}">
                        <x-table.td class="bg-gray-400/10">
                            {{ $promise->id }}
                        </x-table.td>
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
                        <x-table.td>
                            <x-wireui-badge md right-icon="{{ $promise->status->icon() }}" flat rounded color="{{ $promise->status->badge() }}" label="{{ $promise->status->label() }}" />
                        </x-table.td>
                        <x-table.td class="first-letter:uppercase">
                            {{ $promise->signature_date ? $promise->signature_date->translatedFormat("F j/Y") : 'Sin definir' }}
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $promise->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $promise->initial_fee_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
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
                            {{ str($promise->observations)->words(10) }}
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
                        <x-table.actions :item="$promise" :route="route('promises.edit', $promise->id)" />
                    </tr>
                @empty
                    <tr>
                        <x-table.td colspan="12">
                            <div class="flex justify-center items-center gap-2">
                                <x-wireui-icon name="document-search" class="w-8 h-8 text-gray-400" />
                                <span class="font-medium py-8">No se encontraron registros</span>
                            </div>
                        </x-table.td>
                    </tr>
                @endforelse
            </x-slot>
            <x-slot name="foot">
                <tr class="bg-gray-50 dark:bg-white/5">
                    <x-table.th colspan="5">
                        Resumen
                    </x-table.th>
                    <x-table.th class="text-right">
                        Valor Promesa
                    </x-table.th>
                    <x-table.th></x-table.th>
                    <x-table.th></x-table.th>
                    <x-table.th class="text-right">
                        Total Pagado
                    </x-table.th>
                    <x-table.th colspan="8"></x-table.th>
                </tr>
                <tr>
                    <x-table.td colspan="5">
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
                                <span class="text-gray-400">$</span> {{ number_format($promises->sum('value'), 0, ',', '.') }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </x-table.td>
                    <x-table.td></x-table.td>
                    <x-table.td></x-table.td>
                    <x-table.td class="bg-lime-500/10">
                        <div class="flex flex-col items-end gap-1">
                            <p class="font-light text-md">
                                Suma
                            </p>
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ number_format($promises->sum('total_paid'), 0, ',', '.') }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </x-table.td>
                </tr>
                <tr>
                    <x-table.td colspan="5">
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
                                <span class="text-gray-400">$</span> {{ $total_value }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </x-table.td>
                    <x-table.td></x-table.td>
                    <x-table.td></x-table.td>
                    <x-table.td class="bg-lime-500/10">
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
            @if ($promises->hasPages())
                <x-slot name="pagination">
                    {{ $promises->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
