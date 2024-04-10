<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.header :$trash>
            {{-- <x-slot name="import">
                <livewire:deed.index.import />
            </x-slot> --}}
        </x-table.header>
    </div>
    {{-- deeds table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.sortable column="id" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">ID</div>
                    </x-table.sortable>
                    <x-table.sortable column="number" :$sortCol :$sortAsc>
                        Matricula
                    </x-table.sortable>
                    <x-table.sortable column="status" :$sortCol :$sortAsc>
                        Estado
                    </x-table.sortable>
                    <x-table.sortable column="signature_date" :$sortCol :$sortAsc>
                        Fecha Firma
                    </x-table.sortable>
                    <x-table.sortable column="value" :$sortCol :$sortAsc right>
                        Valor
                    </x-table.sortable>
                    <x-table.sortable column="book" :$sortCol :$sortAsc>
                        Libro
                    </x-table.sortable>
                    <x-table.th>
                        Comprador
                    </x-table.th>
                    <x-table.th>
                        Promesa
                    </x-table.th>
                    <x-table.th>
                        Valor Promesa
                    </x-table.th>
                    <x-table.th>
                        Lotes
                    </x-table.th>
                    <x-table.sortable column="observations" :$sortCol :$sortAsc>
                        Observaciones
                    </x-table.sortable>
                    <x-table.th>
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($deeds as $deed)
                    <tr wire:key="{{ $deed->id }}">
                        <x-table.td class="bg-black/5">
                            {{ $deed->id }}
                        </x-table.td>
                        <x-table.td>
                            {{ $deed->number }}
                        </x-table.td>
                        <x-table.td>
                            <x-wireui-badge md right-icon="{{ $deed->status->icon() }}" flat rounded color="{{ $deed->status->badge() }}" label="{{ $deed->status->label() }}" />
                        </x-table.td>
                        <x-table.td class="first-letter:uppercase">
                            {{ $deed->signature_date ? $deed->signature_date->translatedFormat("F j/Y") : 'Sin definir' }}
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $deed->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td>
                            {{ $deed->book ?? 'Sin definir' }}
                        </x-table.td>
                        <x-table.td>
                            @if ($deed->promise)
                                <div class="flex flex-col gap-1 max-h-20 soft-scrollbar overflow-auto">
                                    @forelse ($deed->promise->buyers as $user)
                                        <span class="text-xs text-gray-600 dark:text-gray-300">{{ $user->names }} {{ $user->surnames }}</span>
                                    @empty
                                        <span class="text-xs text-gray-600 dark:text-gray-300">Sin compradores</span>
                                    @endforelse
                                </div>
                            @else
                                <span class="text-xs text-gray-600 dark:text-gray-300">Sin compradores</span>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if ($deed->promise)
                                <span class="text-xs text-gray-600 dark:text-gray-300">{{ $deed->promise->number }}</span>
                            @else
                                <span class="text-xs text-gray-600 dark:text-gray-300">Sin promesa</span>
                            @endif
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $deed->promise ? $deed->promise->value_formatted : 'Sin definir' }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td>
                            @if ($deed->promise->parcels->isNotEmpty())
                                {!! $deed->promise->parcels->groupBy('block_id')->map(function ($parcels) {
                                    return '<span class="dark:text-amber-500 text-amber-600 font-bold">' . $parcels->first()->block->code . ' : </span>' . $parcels->pluck('number')->join(', ');
                                })->join('<br>'); !!}
                            @else
                                <span class="text-xs text-gray-400">Sin lotes</span>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            {{ str($deed->observations)->words(20) }}
                        </x-table.td>
                        <x-table.actions :item="$deed" :route="route('deeds.edit', $deed->id)" />
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
            @if ($deeds->hasPages())
                <x-slot name="pagination">
                    {{ $deeds->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
