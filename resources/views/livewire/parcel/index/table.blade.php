<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.search />
        {{-- <x-parcel.index.bulk-actions /> --}}
    </div>
    {{-- parcels table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.sortable column="id" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">ID</div>
                    </x-table.sortable>
                    <x-table.sortable column="number" :$sortCol :$sortAsc class="bg-lime-500/10">
                        Codigo
                    </x-table.sortable>
                    <x-table.sortable class="bg-pink-500/10" column="block" :$sortCol :$sortAsc>
                        Manzana
                    </x-table.sortable>
                    <x-table.sortable column="area_m2" :$sortCol :$sortAsc right>
                        Area
                    </x-table.sortable>
                    <x-table.sortable column="value" :$sortCol :$sortAsc right>
                        Valor
                    </x-table.sortable>
                    <x-table.sortable column="promise_id" :$sortCol :$sortAsc>
                        Estado
                    </x-table.sortable>
                    <x-table.th>
                        Campaña
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($parcels as $parcel)
                    <tr wire:key="{{ $parcel->id }}">
                        <x-table.td>
                            <div class="flex gap-1">
                                <span class="text-gray-300 dark:text-gray-600">#</span>
                                {{ $parcel->id }}
                            </div>
                        </x-table.td>
                        <x-table.td class="bg-lime-500/10">
                            <p class="font-medium text-lg">
                                {{ $parcel->number }}
                            </p>
                        </x-table.td>
                        <x-table.td class="bg-pink-500/10">
                            <p class="font-medium text-lg">
                                {{ $parcel->block->code }}
                            </p>
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="text-orange-600 font-light text-lg">
                                {{ number_format($parcel->area_m2, 0) }}m²
                            </p>
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $parcel->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td>
                            @if ($parcel->promise_id)
                                <x-wireui-badge lg icon="{{ $parcel->promise->status->icon() }}" flat rounded color="{{ $parcel->promise->status->badge() }}" label="{{ $parcel->promise->status->label() }}" />
                            @else
                                <x-wireui-badge lg icon="lock-open" rounded indigo flat label="Disponible" />
                            @endif
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium text-lg">
                                {{ $parcel->block->category->name }}
                            </p>
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
            @if ($parcels->hasPages())
                <x-slot name="pagination">
                    {{ $parcels->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
