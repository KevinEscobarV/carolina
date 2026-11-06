@props(['number', 'title', 'description' => null])

<div class="flex items-start gap-3">
    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-primary-500/10 font-bold text-primary-600 dark:text-primary-400">{{ $number }}</span>
    <div>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h2>
        @if ($description)
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
        @endif
    </div>
</div>
