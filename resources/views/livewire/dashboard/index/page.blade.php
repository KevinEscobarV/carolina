<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Panel administrativo
    </h2>
</x-slot>

@php
    $money = fn ($value) => '$'.number_format((float) $value, 0, ',', '.');
    $percent = fn ($part, $whole) => $whole > 0 ? min(100, round($part / $whole * 100, 1)) : 0;
    $card = 'rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10 sm:p-6';
    $collectedShare = $percent($totals['collected'], $totals['sales']);
    $lotsShare = $percent($totals['lotsSold'], $totals['lots']);
    $statuses = [
        'up_to_date' => ['color' => 'bg-[#0ca30c]', 'icon' => 'check-circle', 'text' => 'text-[#006300] dark:text-[#0ca30c]'],
        'up_to_30' => ['color' => 'bg-[#fab219]', 'icon' => 'clock', 'text' => 'text-amber-700 dark:text-[#fab219]'],
        'up_to_90' => ['color' => 'bg-[#ec835a]', 'icon' => 'exclamation-triangle', 'text' => 'text-orange-700 dark:text-[#ec835a]'],
        'over_90' => ['color' => 'bg-[#d03b3b]', 'icon' => 'exclamation-circle', 'text' => 'text-red-700 dark:text-[#e66767]'],
    ];
    $agingMax = max(1, max(array_column($aging, 'count')));
    $methodMax = max(1, $methods[0]['amount'] ?? 1);
    $salesMax = max(1, max(array_column($sales['months'], 'count')));
@endphp

<div class="mx-auto flex flex-col gap-6 sm:px-6 lg:px-8">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Campaña <strong class="font-semibold text-gray-700 dark:text-gray-200">{{ $campaign }}</strong>
        · cifras a {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}. Las promesas canceladas no se cuentan.
    </p>

    {{-- Headline figures --}}
    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="{{ $card }} flex flex-col gap-3">
            <dt class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                <x-icon name="document-text" class="h-5 w-5" /> Ventas
            </dt>
            <dd class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $money($totals['sales']) }}</dd>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($totals['promises'], 0, ',', '.') }} promesas activas</p>
            <div class="mt-auto">
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>Lotes vendidos</span>
                    <span><strong class="text-gray-700 dark:text-gray-200">{{ number_format($totals['lotsSold'], 0, ',', '.') }}</strong> de {{ number_format($totals['lots'], 0, ',', '.') }}</span>
                </div>
                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10" role="meter" aria-valuenow="{{ $lotsShare }}" aria-valuemin="0" aria-valuemax="100" aria-label="Lotes vendidos">
                    <div class="h-full rounded-full bg-[#2a78d6] dark:bg-[#3987e5]" style="width: {{ $lotsShare }}%"></div>
                </div>
            </div>
        </div>

        <div class="{{ $card }} flex flex-col gap-3">
            <dt class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                <x-icon name="banknotes" class="h-5 w-5" /> Recaudado
            </dt>
            <dd class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $money($totals['collected']) }}</dd>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Este mes {{ $money($totals['thisMonth']) }} · el anterior {{ $money($totals['lastMonth']) }}
            </p>
            <div class="mt-auto">
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>De las ventas</span>
                    <strong class="text-gray-700 dark:text-gray-200">{{ number_format($collectedShare, 1, ',', '.') }} %</strong>
                </div>
                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10" role="meter" aria-valuenow="{{ $collectedShare }}" aria-valuemin="0" aria-valuemax="100" aria-label="Recaudado de las ventas">
                    <div class="h-full rounded-full bg-[#2a78d6] dark:bg-[#3987e5]" style="width: {{ $collectedShare }}%"></div>
                </div>
            </div>
        </div>

        <div class="{{ $card }} flex flex-col gap-3">
            <dt class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                <x-icon name="scale" class="h-5 w-5" /> Por cobrar
            </dt>
            <dd class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $money($totals['receivable']) }}</dd>
            <p class="text-sm text-gray-500 dark:text-gray-400">Saldo pendiente de las promesas activas</p>
            <p class="mt-auto rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-600 dark:bg-white/5 dark:text-gray-300">
                Próximos 30 días: <strong>{{ $money($upcoming['total']) }}</strong> en {{ $upcoming['count'] }} {{ $upcoming['count'] === 1 ? 'cuota' : 'cuotas' }}
            </p>
        </div>

        <div class="{{ $card }} flex flex-col gap-3">
            <dt class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                <x-icon name="exclamation-circle" class="h-5 w-5 text-[#d03b3b]" /> En mora
            </dt>
            <dd class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $money($totals['overdue']) }}</dd>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $totals['overduePromises'] }} {{ $totals['overduePromises'] === 1 ? 'promesa atrasada' : 'promesas atrasadas' }}
            </p>
            @can('send.messages')
                <a href="{{ route('notifications') }}" class="mt-auto inline-flex items-center gap-1 text-sm font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400">
                    Ver en Notificaciones <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            @endcan
        </div>
    </dl>

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12">
        <div class="flex min-w-0 flex-col gap-6 lg:col-span-8">
            {{-- Monthly collections against what was scheduled --}}
            <section class="{{ $card }}" x-data="monthlyChart(@js($monthly))">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recaudo mensual</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Lo recaudado frente a lo programado (cuotas y cuotas iniciales). A la derecha, lo que vence en los próximos meses.</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-4 text-sm text-gray-600 dark:text-gray-300">
                        <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded-sm bg-[#2a78d6] dark:bg-[#3987e5]"></span>Recaudado</span>
                        <span class="flex items-center gap-1.5"><span class="h-0.5 w-4 rounded bg-[#eb6834] dark:bg-[#d95926]"></span>Programado</span>
                        <button type="button" x-on:click="showTable = ! showTable" class="rounded-lg px-2 py-1 text-xs font-semibold text-primary-600 ring-1 ring-inset ring-gray-200 hover:bg-gray-50 dark:text-primary-400 dark:ring-white/10 dark:hover:bg-white/5"
                            x-text="showTable ? 'Ver gráfica' : 'Ver tabla'"></button>
                    </div>
                </div>

                <div x-show="! showTable" class="mt-6 h-72 [--series-1:#2a78d6] [--series-2:#eb6834] [--grid:#eef0f3] [--axis:#d1d5db] [--muted:#6b7280] [--future:rgba(15,23,42,0.035)] [--future-text:#6b7280] dark:[--series-1:#3987e5] dark:[--series-2:#d95926] dark:[--grid:#2f3947] dark:[--axis:#4b5563] dark:[--muted:#9ca3af] dark:[--future:rgba(255,255,255,0.04)] dark:[--future-text:#9ca3af]" wire:ignore>
                    <canvas x-ref="canvas" role="img" aria-label="Recaudo mensual frente a lo programado"></canvas>
                </div>

                <div x-show="showTable" x-cloak class="mt-6 max-h-72 overflow-auto soft-scrollbar">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 bg-white text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <tr>
                                <th class="py-2 pr-4">Mes</th>
                                <th class="py-2 pr-4 text-right">Programado</th>
                                <th class="py-2 pr-4 text-right">Recaudado</th>
                                <th class="py-2 text-right">Cumplimiento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 tabular-nums text-gray-700 dark:divide-white/5 dark:text-gray-300">
                            @foreach ($monthly['months'] as $index => $month)
                                <tr>
                                    <td class="py-2 pr-4 first-letter:uppercase">{{ $month }}</td>
                                    <td class="py-2 pr-4 text-right">{{ $money($monthly['scheduled'][$index]) }}</td>
                                    <td class="py-2 pr-4 text-right">{{ $monthly['collected'][$index] === null ? '—' : $money($monthly['collected'][$index]) }}</td>
                                    <td class="py-2 text-right">
                                        {{ $monthly['collected'][$index] === null || $monthly['scheduled'][$index] === 0 ? '—' : number_format($monthly['collected'][$index] / $monthly['scheduled'][$index] * 100, 0, ',', '.').' %' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Quotas due soon --}}
            <section class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Próximos vencimientos</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Cuotas de promesas al día que vencen en los próximos 30 días.</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-700 dark:bg-white/10 dark:text-gray-200">{{ $money($upcoming['total']) }}</span>
                </div>

                @if ($upcoming['items'])
                    <ul role="list" class="mt-4 divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($upcoming['items'] as $item)
                            <li class="flex items-center gap-4 py-2.5">
                                <span class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl bg-gray-50 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                                    <span class="text-base font-bold leading-none text-gray-900 dark:text-white">{{ $item['due_date']->format('d') }}</span>
                                    <span class="text-[10px] font-semibold uppercase text-gray-500 dark:text-gray-400">{{ $item['due_date']->locale('es')->isoFormat('MMM') }}</span>
                                </span>
                                <div class="min-w-0 flex-1">
                                    @can('edit.promises')
                                        <a href="{{ route('promises.edit', $item['promise']) }}" class="font-semibold text-gray-900 hover:text-primary-600 dark:text-white dark:hover:text-primary-400">{{ $item['promise']->number }}</a>
                                    @else
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $item['promise']->number }}</span>
                                    @endcan
                                    <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item['promise']->buyers->map(fn ($buyer) => $buyer->names.' '.$buyer->surnames)->join(', ') ?: 'Sin compradores' }}
                                    </p>
                                </div>
                                <span class="shrink-0 font-semibold text-gray-900 dark:text-white">{{ $money($item['amount']) }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @if ($upcoming['count'] > count($upcoming['items']))
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Y {{ $upcoming['count'] - count($upcoming['items']) }} más.</p>
                    @endif
                @else
                    <p class="mt-6 text-sm text-gray-500 dark:text-gray-400">No hay cuotas por vencer en los próximos 30 días.</p>
                @endif
            </section>
        </div>

        <div class="flex min-w-0 flex-col gap-6 lg:col-span-4">
            {{-- Arrears by age --}}
            <section class="{{ $card }}">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Estado de la cartera</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Promesas según los días de atraso de su cuota pendiente.</p>

                <ul role="list" class="mt-6 flex flex-col gap-5">
                    @foreach ($aging as $group)
                        @php($status = $statuses[$group['key']])
                        <li>
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="flex items-center gap-2 font-medium {{ $status['text'] }}">
                                    <x-icon :name="$status['icon']" class="h-5 w-5" /> {{ $group['label'] }}
                                </span>
                                <span class="text-gray-900 dark:text-white"><strong>{{ $group['count'] }}</strong> <span class="text-gray-500 dark:text-gray-400">{{ $group['count'] === 1 ? 'promesa' : 'promesas' }}</span></span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                <div class="h-full rounded-full {{ $status['color'] }}" style="width: {{ $group['count'] / $agingMax * 100 }}%"></div>
                            </div>
                            @if ($group['amount'] > 0)
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $money($group['amount']) }} en mora</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Promises signed per month --}}
            <section class="{{ $card }}">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Ventas recientes</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Promesas firmadas en los últimos 12 meses.</p>

                <p class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $sales['count'] }} {{ $sales['count'] === 1 ? 'promesa' : 'promesas' }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $money($sales['value']) }}</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Los 12 meses anteriores: {{ $sales['previousCount'] }} · {{ $money($sales['previousValue']) }}
                </p>

                <div class="mt-5 flex h-20 items-end gap-1" role="img" aria-label="Promesas firmadas por mes">
                    @foreach ($sales['months'] as $month)
                        <div class="group relative flex h-full flex-1 items-end">
                            <div class="w-full rounded-t-[3px] transition-opacity group-hover:opacity-80 {{ $month['count'] === 0 ? 'bg-gray-200 dark:bg-white/10' : 'bg-[#2a78d6] dark:bg-[#3987e5]' }}"
                                style="height: {{ max(4, $month['count'] / $salesMax * 100) }}%"></div>
                            <div class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-2 hidden w-max -translate-x-1/2 rounded-lg bg-gray-900 px-2.5 py-1.5 text-xs text-white shadow-lg group-hover:block dark:bg-gray-700">
                                <span class="first-letter:uppercase block">{{ $month['label'] }}</span>
                                <strong>{{ $month['count'] }} {{ $month['count'] === 1 ? 'promesa' : 'promesas' }}</strong> · {{ $money($month['value']) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-1.5 flex justify-between border-t border-gray-200 pt-1.5 text-xs text-gray-500 dark:border-white/10 dark:text-gray-400">
                    <span>{{ \Illuminate\Support\Str::ucfirst($sales['months'][0]['label']) }}</span>
                    <span>Este mes</span>
                </div>
            </section>

            {{-- Payment methods --}}
            <section class="{{ $card }}">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Medios de pago</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Recaudo de los últimos 12 meses.</p>

                @if ($methods)
                    <ul role="list" class="mt-5 flex flex-col gap-4">
                        @foreach ($methods as $method)
                            <li>
                                <div class="flex items-baseline justify-between gap-3 text-sm">
                                    <span class="font-medium text-gray-700 dark:text-gray-200">{{ $method['label'] }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ number_format($method['share'], 1, ',', '.') }} %</span>
                                </div>
                                <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                    <div class="h-full rounded-full bg-[#2a78d6] dark:bg-[#3987e5]" style="width: {{ $method['amount'] / $methodMax * 100 }}%"></div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $money($method['amount']) }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-5 text-sm text-gray-500 dark:text-gray-400">No hay pagos en los últimos 12 meses.</p>
                @endif
            </section>
        </div>
    </div>
</div>

@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.js"></script>
@endassets

@script
    <script>
        Alpine.data('monthlyChart', (series) => {
            let chart
            let observer

            const money = (value) => '$' + Math.round(value).toLocaleString('es-CO')
            const compact = (value) => {
                if (value >= 1e9) return '$' + (value / 1e9).toLocaleString('es-CO', { maximumFractionDigits: 1 }) + ' mil M'
                if (value >= 1e6) return '$' + (value / 1e6).toLocaleString('es-CO', { maximumFractionDigits: 1 }) + ' M'
                return money(value)
            }

            // Shades the months still to come, so the projection reads apart from what happened
            const futureBand = {
                id: 'futureBand',
                beforeDatasetsDraw(chart, args, options) {
                    const { ctx, chartArea, scales: { x } } = chart
                    const step = x.getPixelForValue(1) - x.getPixelForValue(0)
                    const from = x.getPixelForValue(series.firstFuture) - step / 2

                    ctx.save()
                    ctx.fillStyle = options.color
                    ctx.fillRect(from, chartArea.top, chartArea.right - from, chartArea.bottom - chartArea.top)
                    ctx.fillStyle = options.text
                    ctx.font = '600 11px Figtree, system-ui, sans-serif'
                    ctx.fillText('PRÓXIMOS MESES', from + 8, chartArea.top + 14)
                    ctx.restore()
                },
            }

            return {
                showTable: false,

                init() {
                    this.draw()
                    observer = new MutationObserver(() => this.draw())
                    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] })
                },

                destroy() {
                    observer?.disconnect()
                    chart?.destroy()
                },

                draw() {
                    chart?.destroy()
                    const style = getComputedStyle(this.$refs.canvas.parentElement)
                    const color = (name) => style.getPropertyValue(name).trim()
                    const font = { family: 'Figtree, system-ui, sans-serif' }

                    chart = new Chart(this.$refs.canvas, {
                        data: {
                            labels: series.labels,
                            datasets: [{
                                type: 'bar',
                                label: 'Recaudado',
                                data: series.collected,
                                backgroundColor: color('--series-1'),
                                borderRadius: 4,
                                borderSkipped: 'start',
                                maxBarThickness: 24,
                                order: 2,
                            }, {
                                type: 'line',
                                label: 'Programado',
                                data: series.scheduled,
                                borderColor: color('--series-2'),
                                backgroundColor: color('--series-2'),
                                borderWidth: 2,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                tension: 0.25,
                                order: 1,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: { display: false },
                                futureBand: { color: color('--future'), text: color('--future-text') },
                                tooltip: {
                                    filter: (item) => item.raw !== null,
                                    callbacks: {
                                        title: (items) => series.months[items[0].dataIndex],
                                        label: (item) => ' ' + item.dataset.label + ': ' + money(item.raw),
                                    },
                                },
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    border: { color: color('--axis') },
                                    ticks: { color: color('--muted'), maxRotation: 0, autoSkipPadding: 12, font },
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: { color: color('--grid') },
                                    border: { display: false },
                                    ticks: { color: color('--muted'), maxTicksLimit: 5, callback: (value) => compact(value), font },
                                },
                            },
                        },
                        plugins: [futureBand],
                    })
                },
            }
        })
    </script>
@endscript
