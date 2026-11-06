@props(['dark' => false])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-3']) }}>
    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-600 text-white shadow-lg shadow-orange-500/30 sm:h-11 sm:w-11">
        <x-icon name="map-pin" class="h-6 w-6" />
    </span>
    <span class="flex flex-col whitespace-nowrap leading-none">
        <span class="text-[0.6rem] font-bold uppercase tracking-[0.35em] text-orange-500 sm:text-[0.65rem]">Loteos</span>
        <span @class([
            'font-display text-base font-extrabold tracking-tight sm:text-lg',
            'text-white' => $dark,
            'text-stone-900' => ! $dark,
        ])>Carolina Riveros</span>
    </span>
</span>
