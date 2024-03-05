<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.search />
        {{-- <x-deed.index.bulk-actions /> --}}
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
                    <x-table.sortable column="parcel" :$sortCol :$sortAsc>
                        Lote
                    </x-table.sortable>
                    <x-table.sortable column="book" :$sortCol :$sortAsc>
                        Libro
                    </x-table.sortable>
                    <x-table.sortable column="observations" :$sortCol :$sortAsc>
                        Obsservaciones
                    </x-table.sortable>
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
                            <x-wireui-badge lg right-icon="{{ $deed->status->icon() }}" flat rounded color="{{ $deed->status->badge() }}" label="{{ $deed->status->label() }}" />
                        </x-table.td>
                        <x-table.td>
                            {{ $deed->signature_date->translatedFormat("F j/Y") }}
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $deed->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td>
                            {{ $deed->parcel->number }}
                        </x-table.td>
                        <x-table.td>
                            {{ $deed->book }}
                        </x-table.td>
                        <x-table.td>
                            {{ str($deed->observations)->words(20) }}
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
            @if ($deeds->hasPages())
                <x-slot name="pagination">
                    {{ $deeds->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
