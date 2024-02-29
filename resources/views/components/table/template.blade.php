<div>
    <div class="relative">
        <div class="overflow-x-auto soft-scrollbar">
            {{-- payments table... --}}
            <table class="min-w-full table-fixed divide-y divide-gray-300 dark:divide-gray-700 text-gray-800">
                <thead class="bg-gray-50 dark:bg-white/5">
                    {{ $head }}
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700 dark:divide-gray-700 dark:text-gray-300">
                    {{ $body }}
                </tbody>
            </table>
        </div>

        {{-- Table loading spinners... --}}
        <div wire:loading wire:target="sortBy, search, nextPage, gotoPage, previousPage, delete, deleteSelected"
            class="absolute inset-0 bg-white dark:bg-black opacity-50">
            {{--  --}}
        </div>

        <div wire:loading.flex wire:target="sortBy, search, nextPage, gotoPage, previousPage, delete, deleteeSelected"
            class="flex justify-center items-center absolute inset-0">
            <x-icon.spinner size="10" class="text-gray-500" />
        </div>
    </div>

    {{-- Pagination... --}}
    @if (isset($pagination))
        <div class="p-6 w-full">
            {{ $pagination }}
        </div>
    @endif
</div>
