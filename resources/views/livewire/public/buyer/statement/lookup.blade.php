<div>
    <form wire:submit="explore" class="flex flex-col gap-3 sm:flex-row">
        <label for="document_number" class="sr-only">Número de documento</label>
        <div class="relative flex-1">
            <x-icon name="identification" class="pointer-events-none absolute left-4 top-1/2 h-6 w-6 -translate-y-1/2 text-stone-400" />
            <input
                id="document_number"
                type="text"
                wire:model="document_number"
                autocomplete="off"
                placeholder="Número de documento"
                class="w-full rounded-2xl border-0 bg-stone-100 py-4 pl-12 pr-4 text-lg text-stone-900 ring-1 ring-inset ring-stone-200 placeholder:text-stone-400 focus:bg-white focus:ring-2 focus:ring-inset focus:ring-orange-500"
            >
        </div>
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="explore"
            class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-500 px-7 py-4 text-lg font-semibold text-white shadow-lg shadow-orange-500/30 transition hover:bg-orange-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2 disabled:opacity-70"
        >
            <x-icon name="magnifying-glass" class="h-5 w-5" wire:loading.remove wire:target="explore" />
            <svg wire:loading wire:target="explore" class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
            Consultar
        </button>
    </form>

    @error('document_number')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if ($searched)
        @if ($promises === [])
            <div role="status" class="mt-5 flex gap-3 rounded-2xl bg-red-50 p-4 text-sm text-red-800 ring-1 ring-inset ring-red-100">
                <x-icon name="exclamation-circle" class="h-5 w-5 shrink-0" />
                <p>No encontramos promesas con ese número de documento. Revísalo o escríbenos por WhatsApp y te ayudamos.</p>
            </div>
        @else
            <ul role="list" class="mt-5 flex flex-col gap-3">
                @foreach ($promises as $promise)
                    <li wire:key="promise-{{ $promise['id'] }}" class="flex flex-col gap-4 rounded-2xl bg-white p-4 ring-1 ring-stone-200 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            @if ($promise['project'])
                                <p class="text-xs font-semibold uppercase tracking-wider text-orange-600">{{ $promise['project'] }}</p>
                            @endif
                            <p class="truncate font-semibold text-stone-900">{{ $promise['number'] }}</p>
                            <p class="text-sm text-stone-500">{{ $promise['date'] }} · ${{ $promise['value'] }} COP</p>
                        </div>
                        <a
                            href="{{ $promise['url'] }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-stone-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-stone-700"
                        >
                            <x-icon name="arrow-down-tray" class="h-4 w-4" />
                            Estado de cuenta (PDF)
                        </a>
                    </li>
                @endforeach
            </ul>
            <p class="mt-3 text-xs text-stone-500">Por seguridad, los enlaces vencen en 30 minutos.</p>
        @endif
    @endif
</div>
