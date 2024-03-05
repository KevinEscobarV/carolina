<div class="divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <x-table.search />
        {{-- <x-block.index.bulk-actions /> --}}
    </div>
    {{-- blocks table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.sortable column="id" :$sortCol :$sortAsc>
                        <div class="whitespace-nowrap">ID</div>
                    </x-table.sortable>
                    <x-table.sortable column="code" :$sortCol :$sortAsc class="bg-pink-500/10">
                        Codigo
                    </x-table.sortable>
                    <x-table.sortable column="area_m2" :$sortCol :$sortAsc>
                        Area
                    </x-table.sortable>
                    <x-table.sortable column="category" :$sortCol :$sortAsc>
                        Campaña
                    </x-table.sortable>
                </tr>
            </x-slot>
            <x-slot name="body">
                @forelse ($blocks as $block)
                    <tr wire:key="{{ $block->id }}">
                        <x-table.td class="bg-black/5">
                            {{ $block->id }}
                        </x-table.td>
                        <x-table.td class="bg-pink-500/10">
                            <p class="font-medium text-lg">
                                {{ $block->code }}
                            </p>
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium text-lg">
                                {{ number_format($block->area_m2, 0) }}m²
                            </p>
                        </x-table.td>
                        <x-table.td>
                            <p class="font-medium text-lg">
                                {{ $block->category->name }}
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
            @if ($blocks->hasPages())
                <x-slot name="pagination">
                    {{ $blocks->links() }}
                </x-slot>
            @endif
    </x-table.template>
</div>
