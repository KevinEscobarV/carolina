<div class="flex flex-col gap-8">
    <div class="flex flex-col sm:grid grid-cols-8 gap-2">
        <div class="col-span-3">
            <x-wireui-input type="text" icon="search" placeholder="Buscar ..." />
        </div>

        <div class="flex justify-end col-span-5">
            <x-wireui-button label="Export" icon="arrow-down" lime />
        </div>
    </div>

    <div class="overflow-x-auto w-full">
        <div class="relative animate-pulse min-w-[49.5rem]">
            <div class="p-3">
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-lg">&nbsp;</div>
            </div>

            <table class="min-w-full table-fixed divide-y divide-gray-300 dark:divide-gray-700 text-gray-800">
                <tbody class="divide-y divide-gray-200 text-gray-700 dark:divide-gray-700 dark:text-gray-300">
                    @foreach (range(0, 8) as $i)
                        <tr>
                            <td class="whitespace-nowrap p-3 text-sm">
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-lg">&nbsp;</div>
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm" colspan="2">
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-lg">&nbsp;</div>
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm" colspan="3">
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-lg">&nbsp;</div>
                            </td>
                            <td class="whitespace-nowrap p-3 text-sm">
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-lg">&nbsp;</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
