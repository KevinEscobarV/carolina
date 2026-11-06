<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Revisión de proyecciones
    </h2>
</x-slot>

@php
    $badges = [
        'out_of_balance' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
        'missing' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
        'unordered_dates' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
        'zero_quotas' => 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300',
    ];
@endphp

<div class="mx-auto flex flex-col gap-6 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-1">
        <p class="text-gray-600 dark:text-gray-400">
            Promesas de esta campaña cuya proyección conviene revisar. Ábrelas para corregirlas en el editor:
            «Ajustar cuotas pendientes» reparte la diferencia y «Agregar cuota» crea la que falta.
        </p>
    </div>

    {{-- Totals by issue, also a filter --}}
    <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
        <button type="button" wire:click="$set('issue', '')" @class([
            'rounded-2xl p-4 text-left shadow-sm ring-1 transition',
            'bg-primary-500 text-white ring-primary-500' => $issue === '',
            'bg-white text-gray-900 ring-gray-950/5 hover:ring-primary-300 dark:bg-gray-800 dark:text-white dark:ring-white/10' => $issue !== '',
        ])>
            <span class="text-sm opacity-80">Por revisar</span>
            <span class="mt-1 block text-2xl font-bold">{{ $total }}</span>
        </button>
        @foreach (App\Livewire\Promise\Review\Page::ISSUES as $type => $label)
            <button type="button" wire:click="$set('issue', '{{ $type }}')" @class([
                'rounded-2xl p-4 text-left shadow-sm ring-1 transition',
                'bg-primary-500 text-white ring-primary-500' => $issue === $type,
                'bg-white text-gray-900 ring-gray-950/5 hover:ring-primary-300 dark:bg-gray-800 dark:text-white dark:ring-white/10' => $issue !== $type,
            ])>
                <span class="text-sm opacity-80">{{ $label }}</span>
                <span class="mt-1 block text-2xl font-bold">{{ $counts[$type] }}</span>
            </button>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
        <div class="overflow-x-auto soft-scrollbar" wire:loading.class="opacity-60">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-white/5 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3">Promesa</th>
                        <th scope="col" class="px-4 py-3">Compradores</th>
                        <th scope="col" class="px-4 py-3">Qué revisar</th>
                        <th scope="col" class="px-4 py-3 text-right">Saldo financiado</th>
                        <th scope="col" class="px-4 py-3 text-right">Suma de cuotas</th>
                        <th scope="col" class="px-4 py-3 text-right">Cuotas</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">Abrir</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 dark:divide-white/5 dark:text-gray-300">
                    @forelse ($reviews as $review)
                        @php($promise = $review['promise'])
                        <tr wire:key="review-{{ $promise->id }}">
                            <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $promise->number }}</td>
                            <td class="px-4 py-3">
                                <div class="flex max-w-xs flex-col gap-0.5 text-xs">
                                    @forelse ($promise->buyers as $buyer)
                                        <span>{{ $buyer->names }} {{ $buyer->surnames }}</span>
                                    @empty
                                        <span class="text-gray-400">Sin compradores</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($review['issues'] as $item)
                                        <span class="whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badges[$item['type']] }}">{{ $item['label'] }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums">
                                <span class="text-gray-400">$</span> {{ number_format((float) $promise->value - (float) $promise->initial_fee, 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right tabular-nums">
                                <span class="text-gray-400">$</span> {{ number_format($promise->quotas->sum('amount'), 0, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">{{ $promise->quotas->count() }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                @can('edit.promises')
                                    <x-wireui-button xs primary href="{{ route('promises.edit', $promise) }}" icon="pencil-square" label="Revisar" />
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                                <x-icon name="check-circle" class="mx-auto mb-2 h-8 w-8 text-emerald-500" />
                                No hay proyecciones por revisar{{ $issue !== '' ? ' con este problema' : '' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
