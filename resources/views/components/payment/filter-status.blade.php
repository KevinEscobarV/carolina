<x-radio-group class="hidden sm:grid grid-cols-4 gap-3" wire:model.live="filters.status">
    @foreach ($filters->statuses() as $status)
        <x-radio-group.option
            :value="$status['value']"
            class="px-3 py-2 flex flex-col rounded-xl border border-gray-300 dark:border-gray-600 hover:border-primary-400 text-gray-700 dark:text-gray-300 cursor-pointer"
            class-checked="text-primary-600 dark:text-primary-500 border-2 border-primary-500 dark:border-primary-500"
            class-not-checked="text-gray-700"
        >
            <div class="text-sm font-normal">
                <span>{{ $status['label'] }}</span>
            </div>

            <div class="text-lg font-semibold">{{ $status['count'] }}</div>
        </x-radio-group.option>
    @endforeach
</x-radio-group>

