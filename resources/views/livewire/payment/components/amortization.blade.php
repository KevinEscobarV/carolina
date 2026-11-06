<div class="col-span-12 lg:col-span-7">
    <x-card>
        @if ($promise)
            @php
                $money = fn ($value) => number_format((float) $value, 0, ',', '.');
                $coverage = $this->coverage();
            @endphp

            <div class="flex flex-col gap-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400">Proyección de pagos</p>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $promise->number }}</h2>
                    </div>
                    <div wire:loading class="ml-auto">
                        <svg class="h-6 w-6 animate-spin text-gray-400" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                    </div>
                </div>

                <dl class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl bg-gray-50 px-4 py-3 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Valor promesa</dt>
                        <dd class="mt-1 font-semibold text-gray-900 dark:text-white">${{ $promise->value_formatted }}</dd>
                    </div>
                    <div class="rounded-xl bg-gray-50 px-4 py-3 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Total pagado</dt>
                        <dd class="mt-1 font-semibold text-emerald-600 dark:text-emerald-400">${{ $promise->total_paid_formatted }}</dd>
                    </div>
                    <div class="rounded-xl bg-gray-50 px-4 py-3 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Saldo</dt>
                        <dd class="mt-1 font-semibold text-gray-900 dark:text-white">${{ $promise->balance_formatted }}</dd>
                    </div>
                </dl>

                @if ($schedule)
                    <x-promise.schedule-editor
                        :rows="$schedule"
                        :version="$scheduleVersion"
                        :principal="$promise->scheduleTerms()->principal"
                        :has-interest="(float) $promise->interest_rate > 0 || collect($schedule)->sum('interest') > 0"
                        :coverage="$coverage"
                        :mode="$promise->rebalanceMode()" />

                    <div class="flex justify-end">
                        <x-wireui-button wire:click="save" spinner="save" primary icon="check" label="Guardar proyección" />
                    </div>
                @else
                    <p class="rounded-xl border-2 border-dashed border-gray-200 px-6 py-8 text-center text-sm text-gray-500 dark:border-white/10 dark:text-gray-400">
                        Esta promesa no tiene proyección de pagos. Genérala desde la edición de la promesa.
                    </p>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center gap-2 px-6 py-10 text-center">
                <x-icon name="table-cells" class="h-8 w-8 text-gray-400" />
                <p class="font-medium text-gray-700 dark:text-gray-300">Selecciona una promesa</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Aquí verás sus cuotas y lo que cubre cada pago.</p>
            </div>
        @endif
    </x-card>
</div>
