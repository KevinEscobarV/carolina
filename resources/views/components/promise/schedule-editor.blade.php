@props([
    'rows' => [],
    'version' => 0,
    'principal' => 0,
    'hasInterest' => false,
    'coverage' => null,
    'mode' => null,
])

@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.');
    $paidCount = $coverage?->paidCount ?? 0;
    $hasPending = $paidCount < count($rows);
    // Money that already covers quotas not yet due: it can become an extra payment
    $today = now()->toDateString();
    $prepaid = 0;
    foreach ($rows as $index => $row) {
        if ($coverage && $row['due_date'] > $today) {
            $prepaid += $coverage->applied[$index] ?? 0;
        }
    }
    $totalAmount = array_sum(array_column($rows, 'amount'));
    $totalInterest = array_sum(array_column($rows, 'interest'));
    $difference = $rows ? (int) end($rows)['balance'] : 0;
    $extraordinaryCount = count(array_filter(array_column($rows, 'is_extraordinary')));
@endphp

<div class="flex flex-col gap-4">
    @if ($rows)
        {{-- Summary --}}
        <dl class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <div class="rounded-xl bg-gray-50 px-4 py-3 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                <dt class="text-xs text-gray-500 dark:text-gray-400">Cuotas</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                    {{ count($rows) }}
                    @if ($extraordinaryCount)
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">· {{ $extraordinaryCount }} extraordinarias</span>
                    @endif
                </dd>
            </div>
            <div class="rounded-xl bg-gray-50 px-4 py-3 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                <dt class="text-xs text-gray-500 dark:text-gray-400">Total a pagar</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">${{ $money($totalAmount) }}</dd>
            </div>
            <div class="rounded-xl bg-gray-50 px-4 py-3 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                <dt class="text-xs text-gray-500 dark:text-gray-400">Saldo financiado</dt>
                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">${{ $money($principal) }}</dd>
            </div>
            <div class="rounded-xl bg-gray-50 px-4 py-3 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                @if ($coverage)
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Pagadas</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $paidCount }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">de {{ count($rows) }}</span></dd>
                @else
                    <dt class="text-xs text-gray-500 dark:text-gray-400">Intereses</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">${{ $money($totalInterest) }}</dd>
                @endif
            </div>
        </dl>

        @if ($mode)
            <p class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                <x-icon name="information-circle" class="mt-0.5 h-5 w-5 shrink-0 text-primary-500" />
                <span>
                    Al cambiar el valor de una cuota, las siguientes se ajustan por <strong class="font-semibold text-gray-800 dark:text-gray-200">{{ mb_strtolower($mode->label()) }}</strong>.
                    Al cambiar una fecha, las siguientes se corren con la periodicidad.
                </span>
            </p>
        @endif

        @if ($prepaid >= 1000 && $hasPending)
            <div class="flex flex-col gap-3 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-900 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-200 dark:ring-emerald-900/50 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-start gap-2">
                    <x-icon name="banknotes" class="h-5 w-5 shrink-0" />
                    <span>
                        Hay <strong>${{ $money($prepaid) }}</strong> pagados por adelantado.
                        Puedes aplicarlos como abono a capital{{ $mode ? ': '.mb_strtolower($mode->label()).'.' : '.' }}
                    </span>
                </p>
                <x-wireui-button wire:click="applyPrepayment" spinner="applyPrepayment" positive sm icon="arrow-path" label="Aplicar abono" />
            </div>
        @endif
    @endif

    @error('schedule')
        <div role="alert" class="flex items-start gap-3 rounded-xl bg-red-50 p-4 text-sm text-red-800 ring-1 ring-inset ring-red-200 dark:bg-red-900/20 dark:text-red-300 dark:ring-red-900/50">
            <x-icon name="exclamation-circle" class="h-5 w-5 shrink-0" />
            <p>{{ $message }}</p>
        </div>
    @enderror

    @if ($rows && abs($difference) > 1)
        <div class="flex flex-col gap-3 rounded-xl bg-amber-50 p-4 text-sm text-amber-900 ring-1 ring-inset ring-amber-200 dark:bg-amber-900/20 dark:text-amber-200 dark:ring-amber-900/50 sm:flex-row sm:items-center sm:justify-between">
            <p class="flex items-start gap-2">
                <x-icon name="exclamation-triangle" class="h-5 w-5 shrink-0" />
                <span>
                    Las cuotas {{ $difference > 0 ? 'no alcanzan a pagar' : 'pagan más que' }} el saldo financiado:
                    {{ $difference > 0 ? 'faltan' : 'sobran' }} <strong>${{ $money(abs($difference)) }}</strong>.
                    @unless ($hasPending)
                        {{ $difference > 0 ? 'Todas las cuotas están pagadas: agrega una cuota para lo que falta.' : 'Todas las cuotas están pagadas.' }}
                    @endunless
                </span>
            </p>
            @if ($hasPending)
                <x-wireui-button wire:click="fitSchedule" spinner="fitSchedule" amber sm icon="scale" label="Ajustar cuotas pendientes" />
            @endif
        </div>
    @endif

    @if ($rows)
        <div class="relative overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
            <div class="max-h-[36rem] overflow-auto soft-scrollbar"
                wire:loading.class="opacity-60" wire:target="schedule,addQuota,removeQuota,toggleExtraordinary,fitSchedule,applyPrepayment,project">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0 z-10 bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">#</th>
                            <th scope="col" class="px-4 py-3">Vence</th>
                            <th scope="col" class="px-4 py-3">Valor cuota</th>
                            @if ($hasInterest)
                                <th scope="col" class="px-4 py-3 text-right">Interés</th>
                                <th scope="col" class="px-4 py-3 text-right">Capital</th>
                            @endif
                            <th scope="col" class="px-4 py-3 text-right">Saldo</th>
                            @if ($coverage)
                                <th scope="col" class="px-4 py-3">Estado</th>
                            @endif
                            <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 dark:divide-white/5 dark:text-gray-300">
                        @foreach ($rows as $index => $row)
                            @php
                                $isPaid = $coverage?->isPaid($index) ?? false;
                                $isPartial = $coverage?->isPartial($index) ?? false;
                            @endphp
                            <tr wire:key="quota-{{ $version }}-{{ $index }}" @class([
                                'bg-emerald-50/60 dark:bg-emerald-900/10' => $isPaid,
                                'bg-amber-50/60 dark:bg-amber-900/10' => ! $isPaid && $row['is_extraordinary'],
                            ])>
                                <td class="whitespace-nowrap px-4 py-2 font-medium text-gray-900 dark:text-white">
                                    {{ $row['number'] }}
                                    @if (! empty($row['is_prepayment']))
                                        <span class="ml-1 rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300" title="Abono a capital">Abono</span>
                                    @elseif ($row['is_extraordinary'])
                                        <span class="ml-1 rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-amber-700 dark:bg-amber-900/40 dark:text-amber-300" title="Cuota extraordinaria">Extra</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    @if ($isPaid)
                                        {{ \Carbon\Carbon::parse($row['due_date'])->translatedFormat('d M Y') }}
                                    @else
                                        <input type="date" wire:model.blur="schedule.{{ $index }}.due_date" aria-label="Fecha de la cuota {{ $row['number'] }}"
                                            class="w-36 rounded-lg border-0 bg-transparent px-2 py-1.5 text-sm text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-primary-500 dark:text-white dark:ring-white/10" />
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-2">
                                    @if ($isPaid)
                                        <span class="text-gray-400">$</span> {{ $money($row['amount']) }}
                                    @else
                                        <div class="w-40">
                                            <x-wireui-inputs.currency wire:model.blur="schedule.{{ $index }}.amount" prefix="$" thousands="." decimal="," precision="0" aria-label="Valor de la cuota {{ $row['number'] }}" />
                                        </div>
                                    @endif
                                </td>
                                @if ($hasInterest)
                                    <td class="whitespace-nowrap px-4 py-2 text-right tabular-nums"><span class="text-gray-400">$</span> {{ $money($row['interest']) }}</td>
                                    <td class="whitespace-nowrap px-4 py-2 text-right tabular-nums"><span class="text-gray-400">$</span> {{ $money($row['principal']) }}</td>
                                @endif
                                <td class="whitespace-nowrap px-4 py-2 text-right tabular-nums"><span class="text-gray-400">$</span> {{ $money($row['balance']) }}</td>
                                @if ($coverage)
                                    <td class="whitespace-nowrap px-4 py-2">
                                        @if ($isPaid)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                                <x-icon name="check" class="h-3.5 w-3.5" /> Pagada
                                            </span>
                                        @elseif ($isPartial)
                                            <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                                Abonado ${{ $money($coverage->applied[$index]) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Pendiente</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="whitespace-nowrap px-4 py-2">
                                    @unless ($isPaid)
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" wire:click="toggleExtraordinary({{ $index }})"
                                                title="{{ $row['is_extraordinary'] ? 'Volver ordinaria' : 'Marcar como extraordinaria' }}"
                                                @class([
                                                    'grid h-8 w-8 place-items-center rounded-lg transition',
                                                    'bg-amber-100 text-amber-600 hover:bg-amber-200 dark:bg-amber-900/40 dark:text-amber-300' => $row['is_extraordinary'],
                                                    'text-gray-400 hover:bg-gray-100 hover:text-amber-500 dark:hover:bg-white/5' => ! $row['is_extraordinary'],
                                                ])>
                                                <x-icon name="star" class="h-4 w-4" />
                                                <span class="sr-only">Cuota extraordinaria</span>
                                            </button>
                                            <button type="button" wire:click="removeQuota({{ $index }})" title="Quitar cuota"
                                                class="grid h-8 w-8 place-items-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20">
                                                <x-icon name="trash" class="h-4 w-4" />
                                                <span class="sr-only">Quitar cuota {{ $row['number'] }}</span>
                                            </button>
                                        </div>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="sticky bottom-0 bg-gray-50 text-sm font-semibold text-gray-900 dark:bg-gray-900 dark:text-white">
                        <tr>
                            <td class="px-4 py-3" colspan="2">Total</td>
                            <td class="whitespace-nowrap px-4 py-3"><span class="text-gray-400">$</span> {{ $money($totalAmount) }}</td>
                            @if ($hasInterest)
                                <td class="whitespace-nowrap px-4 py-3 text-right"><span class="text-gray-400">$</span> {{ $money($totalInterest) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right"><span class="text-gray-400">$</span> {{ $money($totalAmount - $totalInterest) }}</td>
                            @endif
                            <td class="px-4 py-3" colspan="{{ $coverage ? 3 : 2 }}"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div>
            <x-wireui-button wire:click="addQuota" spinner="addQuota" flat primary icon="plus" label="Agregar cuota" />
        </div>
    @else
        <div class="flex flex-col items-center gap-2 rounded-xl border-2 border-dashed border-gray-200 px-6 py-10 text-center dark:border-white/10">
            <x-icon name="calculator" class="h-8 w-8 text-gray-400" />
            <p class="font-medium text-gray-700 dark:text-gray-300">Todavía no hay proyección</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Completa el acuerdo de pago y pulsa «Generar proyección».</p>
        </div>
    @endif
</div>
