<div class="animate-pulse divide-y divide-gray-200 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2 p-6">
        <div class="col-span-3">
            <x-wireui-input type="text" icon="search" placeholder="Buscar ..." disabled />
        </div>
    </div>
    {{-- promises table... --}}
    <x-table.template>
            <x-slot name="head">
                <tr>
                    <x-table.th>
                    </x-table.th>
                </tr>
            </x-slot>
            <x-slot name="body">
                <x-table.td colspan="1">
                    <div class="flex justify-center items-center gap-2">
                        <x-icon.spinner size="10" />
                        <span class="font-medium py-8 text-xl">Cargando...</span>
                    </div>
                </x-table.td>
            </x-slot>
    </x-table.template>
</div>
