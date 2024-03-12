<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.header :$trash />
        {{-- <x-buyer.index.bulk-actions /> --}}
    </div>

    {{-- Buyers table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.sortable column="id" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">ID</div>
                    </x-table.sortable>
                    <x-table.sortable column="names" :$sortCol :$sortAsc>
                        Nombres
                    </x-table.sortable>
                    <x-table.sortable column="surnames" :$sortCol :$sortAsc>
                        Apellidos
                    </x-table.sortable>
                    <x-table.sortable column="email" :$sortCol :$sortAsc>
                        Correo
                    </x-table.sortable>
                    <x-table.sortable column="document_type" :$sortCol :$sortAsc>
                        Tipo
                    </x-table.sortable>
                    <x-table.sortable column="document_number" :$sortCol :$sortAsc>
                        Documento
                    </x-table.sortable>
                    <x-table.sortable column="civil_status" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">Estado Civil</div>
                    </x-table.sortable>
                    <x-table.th>
                        Contacto
                    </x-table.th>
                    <x-table.th>
                        Dirección
                    </x-table.th>
                    <x-table.th>
                        Promesas y Lotes
                    </x-table.th>
                    <x-table.th>
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($buyers as $buyer)
                    <tr wire:key="{{ $buyer->id }}">
                        <x-table.td class="bg-gray-400/10">
                            {{ $buyer->id }}
                        </x-table.td>
                        <x-table.td>
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    <img src="{{ $buyer->profile_photo_url }}" alt="Customer avatar">
                                </div>
                                <div>{{ $buyer->names }}</div>
                            </div>
                        </x-table.td>
                        <x-table.td>
                            {{ $buyer->surnames }}
                        </x-table.td>
                        <x-table.td>
                            {{ $buyer->email }}
                        </x-table.td>
                        <x-table.td>
                            {{ $buyer->document_type->label() }}
                        </x-table.td>
                        <x-table.td>
                            {{ $buyer->document_number }}
                        </x-table.td>
                        <x-table.td>
                            {{ $buyer->civil_status->label() }}
                        </x-table.td>
                        <x-table.td>
                            <div class="flex flex-col gap-1">
                                {{ $buyer->phone_one }}
                                {{ $buyer->phone_two }}
                            </div>
                        </x-table.td>
                        <x-table.td>
                            {{ $buyer->address }}
                        </x-table.td>
                        <td class="border-l border-gray-200 dark:border-gray-700">
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($buyer->promises as $promise)
                                    <div class="flex items-center px-6 py-3">
                                        <div class="flex gap-2 items-center whitespace-nowrap font-medium pr-3">
                                            <p>{{ $promise->number }}</p> <x-icon name="hand-raised" class="w-4 h-4 text-gray-400" />
                                        </div>
                                        <div class="whitespace-nowrap px-3">
                                            {!! $promise->parcels->groupBy('block_id')->map(function ($parcels) {
                                                return '<span class="text-amber-500 font-bold">' . $parcels->first()->block->code . ' : </span>' . $parcels->pluck('number')->join(', ');
                                            })->join('<br>'); !!}
                                        </div>
                                    </div>
                                @empty
                                    <div class="flex justify-center items-center gap-2">
                                        <x-wireui-icon name="document-search" class="w-8 h-8 text-gray-400" />
                                        <span class="font-medium py-8">No se encontraron promesas...</span>
                                    </div>
                                @endforelse
                            </div>
                        </td>
                        <x-table.actions :item="$buyer" :route="route('buyers.edit', $buyer->id)" />
                    </tr>
                @empty
                    <tr>
                        <x-table.td colspan="8">
                            <div class="flex justify-center items-center gap-2">
                                <x-wireui-icon name="document-search" class="w-8 h-8 text-gray-400" />
                                <span class="font-medium py-8">No se encontraron compradores...</span>
                            </div>
                        </x-table.td>
                    </tr>
                @endforelse
            </x-slot>
            @if ($buyers->hasPages())
                <x-slot name="pagination">
                    {{ $buyers->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
