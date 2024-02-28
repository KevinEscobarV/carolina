@props(['active', 'icon' => 'home'])

@php
$classes = ($active ?? false)
            ? 'inline-flex mb-3 items-center gap-2 text-lg leading-4 text-white bg-lime-800/50 dark:bg-lime-300 dark:text-lime-800 p-3 rounded-r-2xl mr-4 hover:bg-lime-700 dark:hover:bg-lime-600 dark:text-gray-200 dark:hover:text-gray-100 transition-all duration-500'
            : 'inline-flex mb-3 items-center gap-2 text-lg leading-4 text-lime-800 hover:text-lime-100 dark:text-lime-200 p-3 rounded-r-2xl mr-4 hover:bg-lime-700 dark:hover:bg-lime-600 dark:text-gray-200 dark:hover:text-gray-100 transition-all duration-500';

$iconClasses = ($active ?? false)
            ? 'w-6 h-6'
            : 'w-6 h-6';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <x-wireui-icon {{ $attributes->merge(['class' => $iconClasses]) }} name="{{ $icon }}" />
    <p class="whitespace-nowrap">
        {{ $slot }}
    </p>
</a>
