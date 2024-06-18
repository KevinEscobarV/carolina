<div class="divide-y divide-gray-200 rounded-t-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.header :$trash />
        {{-- <x-category.index.bulk-actions /> --}}
    </div>
    {{-- categories table... --}}
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
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($categories as $category)
                    <tr wire:key="{{ $category->id }}">
                        <x-table.td>
                            {{ $category->id }}
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium text-2xl">
                                {{ $category->name }}
                            </p>
                        </x-table.td>
                        <x-table.actions :item="$category" model="categories" />
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
            @if ($categories->hasPages())
                <x-slot name="pagination">
                    {{ $categories->links() }}
                </x-slot>
            @endif
    </x-table.template>

    <x-modal wire:model="modal">
        <div class="max-w-4xl">
            <x-card>
                <form wire:submit.prevent="update">
                    <x-category.form />
                    <div class="flex items-center justify-end gap-2 mt-6">
                        <x-wireui-button gray label="Volver" x-on:click="show = false" />
                        <x-wireui-button type="submit" spinner="update" primary label="Actualizar Campaña" />
                    </div>
                </form>
            </x-card>
        </div>
    </x-modal>
</div>
