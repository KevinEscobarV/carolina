<div class="divide-y divide-gray-200 rounded-t-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.header :$trash />
    </div>
    {{-- users table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.sortable column="id" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">ID</div>
                    </x-table.sortable>
                    <x-table.sortable column="name" :$sortCol :$sortAsc>
                        Nombre
                    </x-table.sortable>
                    <x-table.sortable column="email" :$sortCol :$sortAsc>
                        Correo
                    </x-table.sortable>
                    <x-table.th>
                        Rol
                    </x-table.th>
                    <x-table.th>
                        Actualmente en
                    </x-table.th>
                    <x-table.sortable column="updated_at" :$sortCol :$sortAsc>
                        Fecha actualización
                    </x-table.sortable>
                    <x-table.th>
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($users as $user)
                    <tr wire:key="{{ $user->id }}">
                        <x-table.td class="bg-black/5">
                            {{ $user->id }}
                        </x-table.td>
                        <x-table.td>
                            {{ $user->name }}
                        </x-table.td>
                        <x-table.td>
                            {{ $user->email }}
                        </x-table.td>
                        <x-table.td>
                            {{ $user->name_role }}
                        </x-table.td>
                        <x-table.td>
                            {{ $user->currentCategory->name }}
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium first-letter:uppercase text-sm text-gray-400">
                                {{ $user->updated_at->translatedFormat('F j, Y') }}
                            </p>
                        </x-table.td>
                        @if (auth()->user()->id == $user->id)
                            <x-table.td>
                                <div class="flex items-center justify-center gap-2">
                                    <x-wireui-icon name="lock-closed" class="w-6 h-6 text-gray-400" />
                                    <span class="font-medium py-4">No se puede editar</span>
                                </div>
                            </x-table.td>
                        @else
                            <x-table.actions :item="$user" model="users" />
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
            @if ($users->hasPages())
                <x-slot name="pagination">
                    {{ $users->links() }}
                </x-slot>
            @endif
    </x-table.template>

    <x-modal wire:model="modal">
        <div class="max-w-4xl">
            <x-card>
                <form wire:submit.prevent="update">
                    <x-user.form :$roles :$categories />
                    <div class="flex items-center justify-end gap-2 mt-6">
                        <x-wireui-button gray label="Volver" x-on:click="show = false" />
                        <x-wireui-button type="submit" spinner="update" primary label="Actualizar usuario" />
                    </div>
                </form>
            </x-card>
        </div>
    </x-modal>
</div>
