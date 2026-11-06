@props(['delay' => 0])

{{-- Fades the content in when it scrolls into view; without JavaScript it is simply shown --}}
<div
    x-data="{ shown: false }"
    x-intersect.once="shown = true"
    :class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
    @if ($delay) style="transition-delay: {{ (int) $delay }}ms" @endif
    {{ $attributes->merge(['class' => 'transition duration-700 ease-out motion-reduce:translate-y-0 motion-reduce:opacity-100 motion-reduce:transition-none']) }}
>
    {{ $slot }}
</div>
