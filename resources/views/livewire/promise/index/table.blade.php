<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.search />
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
                    <x-table.sortable column="status" :$sortCol :$sortAsc>
                        Estado
                    </x-table.sortable>
                    <x-table.sortable column="signature_date" :$sortCol :$sortAsc>
                        Fecha Firma
                    </x-table.sortable>
                    <x-table.sortable column="value" :$sortCol :$sortAsc>
                        Valor
                    </x-table.sortable>
                    <x-table.sortable column="initial_fee" :$sortCol :$sortAsc>
                        Cuota Inicial
                    </x-table.sortable>
                    <x-table.sortable column="number_of_fees" :$sortCol :$sortAsc>
                        Número de Cuotas
                    </x-table.sortable>
                    <x-table.sortable column="interest_rate" :$sortCol :$sortAsc>
                        Interes
                    </x-table.sortable>
                    <x-table.sortable column="payment_method" :$sortCol :$sortAsc>
                        Metodo de Pago
                    </x-table.sortable>
                    <x-table.sortable column="observations" :$sortCol :$sortAsc>
                        Obsservaciones
                    </x-table.sortable>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($promises as $promise)
                    <tr wire:key="{{ $promise->id }}">
                        <x-table.td>
                            <div class="flex gap-1">
                                <span class="text-gray-300">#</span>
                                {{ $promise->id }}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            {{ $promise->number }}
                        </x-table.td>
                        <x-table.td>
                            <x-wireui-badge lg right-icon="{{ $promise->status->icon() }}" flat rounded color="{{ $promise->status->badge() }}" label="{{ $promise->status->label() }}" />
                        </x-table.td>
                        <x-table.td>
                            {{ $promise->signature_date->translatedFormat("F j/Y") }}
                        </x-table.td>
                        <x-table.td>
                            {{ $promise->value_formatted }}
                        </x-table.td>
                        <x-table.td>
                            {{ $promise->initial_fee_formatted }}
                        </x-table.td>
                        <x-table.td>
                            {{ $promise->number_of_fees }}
                        </x-table.td>
                        <x-table.td>
                            {{ $promise->interest_rate }}%
                        </x-table.td>
                        <x-table.td>
                            {{ $promise->payment_method->label() }}
                        </x-table.td>
                        <x-table.td>
                            {{ str($promise->observations)->words(20) }}
                        </x-table.td>
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
            @if ($promises->hasPages())
                <x-slot name="pagination">
                    {{ $promises->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
