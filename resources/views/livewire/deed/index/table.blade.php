<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.header :$trash>
            <x-slot name="import">
                <livewire:deed.index.import />
            </x-slot>
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
                    <x-table.sortable column="parcel" :$sortCol :$sortAsc>
                        Lote
                    </x-table.sortable>
                    <x-table.sortable column="block" :$sortCol :$sortAsc>
                        Manzana
                    </x-table.sortable>
                    <x-table.th>
                        Promesa
                    </x-table.th>
                    <x-table.th>
                        Compradores
                    </x-table.th>
                    <x-table.sortable column="book" :$sortCol :$sortAsc>
                        Libro
                    </x-table.sortable>
                    <x-table.sortable column="observations" :$sortCol :$sortAsc>
                        Obsservaciones
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
                            <x-wireui-badge lg right-icon="{{ $deed->status->icon() }}" flat rounded color="{{ $deed->status->badge() }}" label="{{ $deed->status->label() }}" />
                        </x-table.td>
                        <x-table.td class="first-letter:uppercase">
                            {{ $deed->signature_date ? $deed->signature_date->translatedFormat("F j/Y") : 'Sin definir' }}
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $deed->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td class="bg-green-400/10 font-medium">
                            @if ($deed->parcel)
                                {{ $deed->parcel->number }}
                            @else
                                Lote no asignado
                            @endif
                        </x-table.td>
                        <x-table.td class="bg-pink-400/10 font-medium">
                            @if ($deed->parcel)
                                {{ $deed->parcel->block->code }}
                            @else
                                Lote no asignado
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if ($deed->parcel)
                                {{ $deed->parcel->promise ? $deed->parcel->promise->number : 'Sin definir' }}
                            @else
                                Lote no asignado
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if ($deed->parcel)
                                @if ($deed->parcel->promise)
                                    <div class="flex flex-col gap-1 max-h-20 soft-scrollbar overflow-auto">
                                        @forelse ($deed->parcel->promise->buyers as $user)
                                            <span class="text-xs text-gray-500 dark:text-gray-300">{{ $user->names }} {{ $user->surnames }}</span>
                                        @empty
                                            <span class="text-xs text-gray-500 dark:text-gray-300">Sin compradores</span>
                                        @endforelse
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-300">Sin promesa</span>
                                @endif
                            @else
                                <span class="text-xs text-gray-500 dark:text-gray-300">Lote no asignado</span>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            {{ $deed->book ?? 'Sin definir' }}
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
