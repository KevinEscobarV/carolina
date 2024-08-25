<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.header :$trash routeCreate="{{route('parcels.create')}}">
            <x-slot name="import">
                <livewire:parcel.index.import-registration-number />
                <livewire:parcel.index.import />
            </x-slot>
        </x-table.header>
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
                    <x-table.sortable column="position" :$sortCol :$sortAsc>
                        Posición
                    </x-table.sortable>
                    <x-table.th>
                        Promesa
                    </x-table.th>
                    <x-table.th>
                        Compradores
                    </x-table.th>
                    <x-table.sortable column="promise_id" :$sortCol :$sortAsc>
                        Estado
                    </x-table.sortable>
                    <x-table.sortable column="registration_number" :$sortCol :$sortAsc>
                        Folio de matricula N°
                    </x-table.sortable>
                    <x-table.sortable column="updated_at" :$sortCol :$sortAsc>
                        Fecha actualización
                    </x-table.sortable>
                    <x-table.th>
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($parcels as $parcel)
                    <tr wire:key="{{ $parcel->id }}">
                        <x-table.td class="bg-black/5">
                            {{ $parcel->id }}
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
                                {{ number_format($parcel->area_m2, 2) }}m²
                            </p>
                        </x-table.td>
                        <x-table.td class="text-right">
                            <p class="font-light text-lg">
                                <span class="text-gray-400">$</span> {{ $parcel->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium">
                                {{ $parcel->position->label() }}
                            </p>
                        </x-table.td>
                        <x-table.td>
                            @if ($parcel->promise_id)
                                <p class="font-medium">
                                    {{ $parcel->promise->number }}
                                </p>
                            @else
                                <p class="font-medium text-sm text-gray-400">
                                    Libre
                                </p>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if ($parcel->promise_id)
                                <div class="flex flex-col gap-1 max-h-20 soft-scrollbar overflow-auto">
                                    @forelse ($parcel->promise->buyers as $user)
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
                            @if ($parcel->promise_id)
                                <x-wireui-badge md icon="{{ $parcel->promise->status->icon() }}" flat rounded color="{{ $parcel->promise->status->badge() }}" label="{{ $parcel->promise->status->label() }}" />
                            @else
                                <x-wireui-badge md icon="lock-open" rounded indigo flat label="Disponible" />
                            @endif
                        </x-table.td>
                        <x-table.td>
                            @if ($parcel->registration_number)
                                <p class="font-medium">
                                    {{ $parcel->registration_number }}
                                </p>
                            @else
                                <p class="font-medium text-sm text-gray-400">
                                    Sin asignar
                                </p>
                            @endif
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium first-letter:uppercase text-sm text-gray-400">
                                {{ $parcel->updated_at->translatedFormat('F j, Y') }}
                            </p>
                        </x-table.td>
                        <x-table.actions :item="$parcel" :route="route('parcels.edit', $parcel->id)" model="parcels" />
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
