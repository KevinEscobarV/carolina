<div>
    <div class="relative">
        <div class="overflow-x-auto soft-scrollbar">
            {{-- payments table... --}}
            <table class="min-w-full table-fixed text-gray-800">
                <thead class="bg-gray-50 dark:bg-white/5 text-left border-b border-gray-200 dark:border-gray-700">
                    {{ $head }}
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700 dark:divide-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700">
                    {{ $body }}
                </tbody>
                @if (isset($foot))
                    <tfoot class="divide-y divide-gray-200 text-gray-700 dark:divide-gray-700 dark:text-gray-300 text-left">
                        {{ $foot }}
                    </tfoot>
                @endif
            </table>
        </div>

        {{-- Table loading spinners... --}}
        <div wire:loading wire:target="sortBy, search, nextPage, gotoPage, previousPage, delete, deleteSelected, perPage, onlyTrash, restore"
            class="absolute inset-0 bg-white dark:bg-black opacity-50">
            {{--  --}}
        </div>

        <div wire:loading.flex wire:target="sortBy, search, nextPage, gotoPage, previousPage, delete, deleteeSelected, perPage, onlyTrash, restore"
            class="flex justify-center items-center absolute inset-0">
            <x-icon.spinner size="10" class="text-gray-500" />
        </div>
    </div>

    {{-- Pagination... --}}
    @if (isset($pagination))
        <div class="p-6 w-full border-t border-gray-300 dark:border-gray-700">
            {{ $pagination }}
        </div>
    @endif
</div>
