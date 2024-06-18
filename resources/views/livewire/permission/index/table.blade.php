<div class="divide-y divide-gray-200 rounded-t-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.header />
    </div>
    {{-- roles table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.sortable column="id" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">ID</div>
                    </x-table.sortable>
                    <x-table.sortable column="name" :$sortCol :$sortAsc>
                        Nombre
                    </x-table.sortable>
                    <x-table.th>
                        Permisos
                    </x-table.th>
                    <x-table.sortable column="updated_at" :$sortCol :$sortAsc>
                        Fecha actualización
                    </x-table.sortable>
                    <x-table.th>
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($roles as $role)
                    <tr wire:key="{{ $role->id }}">
                        <x-table.td class="bg-black/5">
                            {{ $role->id }}
                        </x-table.td>
                        <x-table.td>
                            {{ $role->name }}
                        </x-table.td>
                        <x-table.td>
                            @if ($role->name == 'Administrador')
                                <span class="text-xs font-medium bg-red-500 text-white rounded-full px-2 py-1">Todos</span>
                            @else
                                {{ $role->permissions->count() }}
                            @endif
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium first-letter:uppercase text-sm text-gray-400">
                                {{ $role->updated_at->translatedFormat('F j, Y') }}
                            </p>
                        </x-table.td>
                        
                        @if ($role->name == 'Administrador')
                            <x-table.td>
                                <div class="flex items-center justify-center gap-2">
                                    <x-wireui-icon name="lock-closed" class="w-6 h-6 text-gray-400" />
                                    <span class="font-medium py-4">No se puede editar</span>
                                </div>
                            </x-table.td>
                        @else
                            <x-table.actions-without-softdelete :item="$role" model="roles" />
                        @endif
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
            @if ($roles->hasPages())
                <x-slot name="pagination">
                    {{ $roles->links() }}
                </x-slot>
            @endif
    </x-table.template>

    <x-modal wire:model="modal" maxWidth="5xl">
        <x-card>
            <form wire:submit.prevent="update">
                <x-permission.form :$permissions />
                <div class="flex items-center justify-end gap-2 mt-6">
                    <x-wireui-button gray label="Volver" x-on:click="show = false" />
                    <x-wireui-button type="submit" spinner="update" primary label="Actualizar rol" />
                </div>
            </form>
        </x-card>
    </x-modal>
</div>
