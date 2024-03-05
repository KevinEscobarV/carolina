<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.search />
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
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($categories as $category)
                    <tr wire:key="{{ $category->id }}">
                        <x-table.td class="bg-black/5">
                            {{ $category->id }}
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium text-lg">
                                {{ $category->name }}
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
            @if ($categories->hasPages())
                <x-slot name="pagination">
                    {{ $categories->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
