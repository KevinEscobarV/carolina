@props(['active', 'icon' => 'home'])

@php
$classes = ($active ?? false)
            ? 'inline-flex mb-3 items-center gap-2 text-lg leading-4 text-white bg-primary-500 dark:bg-primary-600 dark:text-white p-3 rounded-r-xl mr-4 hover:bg-primary-700 dark:hover:bg-primary-600 dark:text-gray-200 dark:hover:text-gray-100 transition-all duration-300'
            : 'inline-flex mb-3 items-center gap-2 text-lg leading-4 text-secondary-600 hover:text-secondary-100 dark:text-secondary-200 p-3 rounded-r-xl mr-4 hover:bg-primary-500 dark:hover:bg-primary-600/70 dark:text-gray-200 dark:hover:text-gray-100 transition-all duration-300';

$iconClasses = ($active ?? false)
            ? 'w-6 h-6'
            : 'w-6 h-6';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <x-icon {{ $attributes->merge(['class' => $iconClasses]) }} name="{{ $icon }}" />
    <p class="whitespace-nowrap">
        {{ $slot }}
    </p>
</a>
