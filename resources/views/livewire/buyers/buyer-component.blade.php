<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Compradores
    </h2>
</x-slot>

<div class="container mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">
    <x-card title="Nuevo Comprador">
        <x-slot name="action">
            <x-wireui-icon name="user" class="w-6 h-6 text-gray-500" />
        </x-slot>
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-6 gap-4">
                <div class="col-span-6 sm:col-span-3">
                    <x-wireui-input label="Nombres" placeholder="Nombres del Usuario" wire:model="names" />
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <x-wireui-textarea label="Descripción (Opcional)" placeholder="Descripción del Capuchón"
                        wire:model="description" />
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button type="submit" spinner="save" lime label="Crear" />
            </div>
        </form>
    </x-card>

    <x-card title="Tabla de Usuarios">
        <x-slot name="action">
            <x-wireui-icon name="table" class="w-6 h-6 text-gray-500" />
        </x-slot>
        <div class="flex flex-col gap-8">
            <div class="grid grid-cols-2 gap-2">
                <x-wireui-input label="Buscar" placeholder="Buscar Comprador" wire:model.live.debounce.800ms="search" />
            </div>
            <div>
                <div class="relative">
                    <table class="min-w-full table-fixed dived-y divide-gray-300 text-gray-800">
                        <thead>
                            <tr>
                                <th class="p-2 text-left text-sm font-semibold text-gray-900">
                                    <x-sortable column="id" :sortCol :sortAsc>
                                        Numero
                                    </x-sortable>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white text-gray-700">
                            @forelse ($buyers as $buyer)
                                <tr wire:key="{{ $buyer->id }}">
                                    <td class="p-3 text-sm font-semibold">
                                        {{ $buyer->id }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="p-2 text-sm font-semibold text-gray-900" colspan="1">
                                        No hay registros
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div wire.loading class="absolute inset-0 bg-white opacity-50">
                        {{--  --}}
                    </div>

                    <div wire.loading.flex class="flex justify-center items-center absolute inset-0">
                        <x-wireui-icon name="pencil" class="w-4 h-4 text-gray-500" />
                    </div>
                </div>


                <div class="pt-4 flex justify-between items-center">
                    {{ $buyers->links() }}
                </div>

            </div>

        </div>
    </x-card>
</div>
