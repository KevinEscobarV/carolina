<div class="flex flex-col gap-8">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2">
        <x-buyer.index.search />

        <x-buyer.index.bulk-actions />
    </div>

    <div>
        <div class="relative overflow-x-auto soft-scrollbar bg-gray-200 border border-gray-200 rounded-xl">
            {{-- Buyers table... --}}
            <table class="min-w-full table-fixed divide-y divide-gray-300 text-gray-800">
                <thead>
                    <tr>
                        {{-- <th class="p-3 text-left text-sm font-semibold text-gray-900">
                                    <div class="flex items-center">
                                        <x-buyer.index.check-all />
                                    </div>
                                </th> --}}

                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <x-buyer.index.sortable column="id" :$sortCol :$sortAsc>
                                <div class="whitespace-nowrap">ID</div>
                            </x-buyer.index.sortable>
                        </th>

                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <x-buyer.index.sortable column="names" :$sortCol :$sortAsc>
                                <div>Nombres</div>
                            </x-buyer.index.sortable>
                        </th>

                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <x-buyer.index.sortable column="surnames" :$sortCol :$sortAsc>
                                <div>Apellidos</div>
                            </x-buyer.index.sortable>
                        </th>

                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <x-buyer.index.sortable column="email" :$sortCol :$sortAsc>
                                <div>Correo</div>
                            </x-buyer.index.sortable>
                        </th>

                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <x-buyer.index.sortable column="document_type" :$sortCol :$sortAsc>
                                <div>Tipo</div>
                            </x-buyer.index.sortable>
                        </th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <x-buyer.index.sortable column="document_number" :$sortCol :$sortAsc>
                                <div>Documento</div>
                            </x-buyer.index.sortable>
                        </th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <x-buyer.index.sortable column="civil_status" :$sortCol :$sortAsc>
                                <div class="whitespace-nowrap">Estado Civil</div>
                            </x-buyer.index.sortable>
                        </th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <div>Contacto</div>
                        </th>
                        <th class="p-3 text-left text-sm font-semibold text-gray-900">
                            <div>Dirección</div>
                        </th>

                        {{-- <th>
                                </th> --}}
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                    @foreach ($buyers as $buyer)
                        <tr wire:key="{{ $buyer->id }}">
                            {{-- <td class="whitespace-nowrap p-3 text-sm">
                                        <div class="flex items-center">
                                            <input wire:model="selectedBuyerIds" value="{{ $buyer->id }}" type="checkbox" class="rounded border-gray-300 shadow">
                                        </div>
                                    </td> --}}
                            <td class="whitespace-nowrap p-3 text-sm">
                                <div class="flex gap-1">
                                    <span class="text-gray-300">#</span>
                                    {{ $buyer->id }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded-full overflow-hidden">
                                        <img src="{{ $buyer->profile_photo_url }}" alt="Customer avatar">
                                    </div>
                                    <div>{{ $buyer->names }}</div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                {{ $buyer->surnames }}
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                {{ $buyer->email }}
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                {{ $buyer->document_type->label() }}
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                {{ $buyer->document_number }}
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                {{ $buyer->civil_status->label() }}
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                <div class="flex flex-col gap-1">
                                    {{ $buyer->phone_one }}
                                    {{ $buyer->phone_two }}
                                </div>
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                {{ $buyer->address }}
                            </td>
                            {{-- <td class="whitespace-nowrap p-3 text-sm">
                                        <div class="flex items-center justify-end">
                                            <x-buyer.index.row-dropdown :$buyer />
                                        </div>
                                    </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Table loading spinners... --}}
            <div wire:loading wire:target="sortBy, search, nextPage, gotoPage, previousPage, delete, deleteSelected"
                class="absolute inset-0 bg-white opacity-50">
                {{--  --}}
            </div>

            <div wire:loading.flex
                wire:target="sortBy, search, nextPage, gotoPage, previousPage, delete, deleteeSelected"
                class="flex justify-center items-center absolute inset-0">
                <x-icon.spinner size="10" class="text-gray-500" />
            </div>
        </div>

        {{-- Pagination... --}}
        <div class="pt-4 w-full">
            {{ $buyers->links() }}
        </div>
    </div>
</div>