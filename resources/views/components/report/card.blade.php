@props(['icon', 'title', 'description'])

<div class="flex h-full flex-col gap-5 rounded-xl p-4 ring-1 ring-inset ring-gray-200 dark:ring-white/10">
    <div class="flex items-start gap-3">
        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-primary-500/10 text-primary-600 dark:text-primary-400">
            <x-icon :name="$icon" class="h-5 w-5" />
        </span>
        <div>
            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        {{ $slot }}
    </div>

    <div class="mt-auto">
        <x-wireui-button class="w-full" icon="download" primary outline label="Exportar a Excel" wire:click="exportGeneral" spinner="exportGeneral" />
    </div>
</div>
