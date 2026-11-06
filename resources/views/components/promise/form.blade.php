@props(['block', 'isCreate', 'form'])

@php
    $money = fn ($value) => number_format((float) $value, 0, ',', '.');
    $principal = max(0, (float) $form->value - (float) $form->initial_fee);
    $rateType = $form->interest_rate_type instanceof App\Enums\InterestRateType
        ? $form->interest_rate_type
        : (App\Enums\InterestRateType::tryFrom((string) $form->interest_rate_type) ?? App\Enums\InterestRateType::MONTHLY);
    $rate = new App\Services\Amortization\InterestRate((float) $form->interest_rate, $rateType);
    $preview = $form->previewSchedule();
    $previewOrdinary = $preview ? collect($preview)->firstWhere('is_extraordinary', false) : null;
    $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $card = 'rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10 sm:p-6';
    $choice = 'flex h-full cursor-pointer flex-col gap-1 rounded-xl border border-gray-200 bg-white p-4 text-gray-600 transition hover:bg-gray-50 peer-checked:border-primary-500 peer-checked:ring-1 peer-checked:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700/50';
@endphp

<div class="flex flex-col gap-6">
    <div class="grid gap-6 xl:grid-cols-2">
        {{-- 1. Promise and parcels --}}
        <section class="{{ $card }}">
            <x-promise.section-heading number="1" title="Promesa y bienes" description="Compradores, lotes y fechas de firma." />

            <div class="mt-6 grid grid-cols-6 gap-5">
                <div class="col-span-6 sm:col-span-3">
                    <x-wireui-input label="Número o código de la promesa" placeholder="Ej. PROMESA 120" wire:model="form.number" />
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <x-wireui-select label="Estado" placeholder="Seleccione un estado" :options="App\Enums\PromiseStatus::select()"
                        option-label="label" option-value="value" wire:model="form.status" autocomplete="off" />
                </div>
                <div class="col-span-6">
                    <x-wireui-select label="Compradores" wire:model="form.buyers"
                        placeholder="Busque y marque los compradores" :async-data="route('api.buyers.index')" option-label="names"
                        option-value="id" multiselect />
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <x-wireui-select label="Manzana (filtro)" wire:model.live="block"
                        placeholder="Seleccione una manzana" :async-data="route('api.blocks.index')" option-label="code"
                        option-value="id" />
                </div>
                {{-- The key rebuilds the ignored select when the block changes, so it loads that block's parcels --}}
                <div class="col-span-6 sm:col-span-3" wire:ignore wire:key="parcels-{{ $block }}">
                    <x-wireui-select label="Lotes" wire:model.live.debounce.500ms="form.parcels" empty-message="Seleccione una manzana primero"
                        :async-data="route('api.parcels.promises', $block)"
                        placeholder="Marque los lotes" option-label="number"
                        option-value="id" multiselect />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <x-wireui-inputs.currency label="Valor de los bienes" hint="Suma del valor de los lotes" prefix="$" thousands="." decimal="," precision="0" wire:model.live.debounce.600ms="form.value" />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <x-wireui-datetime-picker label="Fecha de firma" placeholder="Fecha de la promesa" wire:model.live="form.signature_date" without-time />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <x-wireui-datetime-picker label="Firma de escritura" placeholder="Opcional" wire:model="form.signature_deed_date" without-time />
                </div>
                <div class="col-span-6">
                    <x-wireui-textarea label="Observaciones" placeholder="Observaciones o concepto" rows="3" wire:model="form.observations" />
                </div>
            </div>
        </section>

        {{-- 2. Payment agreement --}}
        <section class="{{ $card }}">
            <x-promise.section-heading number="2" title="Acuerdo de pago" description="Con estos datos se genera la proyección." />

            <div class="mt-6 grid grid-cols-6 gap-5">
                <div class="col-span-6 sm:col-span-2">
                    <x-wireui-select label="Medio de pago" placeholder="Seleccione" :options="App\Enums\PromisePaymentMethod::select()"
                        option-label="label" option-value="value" wire:model="form.payment_method" autocomplete="off" />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <x-wireui-inputs.currency label="Cuota inicial" prefix="$" thousands="." decimal="," precision="0" wire:model.live.debounce.600ms="form.initial_fee" />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <p class="block text-sm font-medium text-gray-700 dark:text-gray-400">Saldo a financiar</p>
                    <p class="mt-1 rounded-lg bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:text-white dark:ring-white/10">${{ $money($principal) }}</p>
                </div>

                <div class="col-span-6 sm:col-span-2">
                    <x-wireui-inputs.number label="Tasa de interés (%)" placeholder="0" wire:model.live.debounce.600ms="form.interest_rate" step="0.01" min="0" />
                </div>
                <div class="col-span-6 sm:col-span-2">
                    <x-wireui-native-select label="Tipo de tasa" wire:model.live="form.interest_rate_type">
                        @foreach (App\Enums\InterestRateType::cases() as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </x-wireui-native-select>
                </div>
                <div class="col-span-6 flex items-end sm:col-span-2">
                    <p class="w-full rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-600 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10">
                        @if ($rate->isZero())
                            Sin interés
                        @elseif ($rate->type === App\Enums\InterestRateType::MONTHLY)
                            Equivale a <strong>{{ number_format($rate->effectiveAnnual() * 100, 2, ',', '.') }} % E.A.</strong>
                        @else
                            Equivale a <strong>{{ number_format($rate->monthly() * 100, 2, ',', '.') }} % mensual</strong>
                        @endif
                    </p>
                </div>

                <div class="col-span-6 sm:col-span-3">
                    <x-wireui-native-select label="Periodicidad" wire:model.live="form.payment_frequency">
                        @foreach (App\Enums\PaymentFrequency::cases() as $frequency)
                            <option value="{{ $frequency->value }}">{{ $frequency->label() }}{{ $frequency === App\Enums\PaymentFrequency::IRREGULAR ? ' (fechas y valores a mano)' : '' }}</option>
                        @endforeach
                    </x-wireui-native-select>
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <x-wireui-datetime-picker label="Fecha del primer pago" placeholder="Fecha de corte" wire:model.live="form.cut_off_date" without-time />
                </div>

                {{-- How the schedule is generated --}}
                <fieldset class="col-span-6">
                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-400">Generar la proyección por</legend>
                    <div class="mt-2 grid gap-3 sm:grid-cols-2">
                        <label class="block">
                            <input type="radio" wire:model.live="form.switch_quota" value="1" class="peer sr-only">
                            <span class="{{ $choice }}">
                                <span class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                    <x-icon name="hashtag" class="h-5 w-5 text-primary-500" /> Número de cuotas
                                </span>
                                <span class="text-sm">Se calcula el valor de cada cuota.</span>
                            </span>
                        </label>
                        <label class="block">
                            <input type="radio" wire:model.live="form.switch_quota" value="0" class="peer sr-only">
                            <span class="{{ $choice }}">
                                <span class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                    <x-icon name="banknotes" class="h-5 w-5 text-primary-500" /> Valor de la cuota
                                </span>
                                <span class="text-sm">Se calcula cuántas cuotas hacen falta.</span>
                            </span>
                        </label>
                    </div>
                    <div class="mt-3 max-w-xs">
                        @if ($form->switch_quota)
                            <x-wireui-inputs.number label="Número de cuotas" wire:model.live.debounce.600ms="form.number_of_fees" min="1" max="600" />
                        @else
                            <x-wireui-inputs.currency label="Valor de la cuota" prefix="$" thousands="." decimal="," precision="0" wire:model.live.debounce.600ms="form.quota_amount" />
                        @endif
                    </div>
                </fieldset>

                {{-- Extraordinary quotas --}}
                <div class="col-span-6 rounded-xl bg-gray-50 p-4 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:ring-white/10">
                    <x-wireui-toggle wire:model.live="form.has_extraordinary" label="Cuotas extraordinarias (por ejemplo, primas de junio y diciembre)" />
                    @if ($form->has_extraordinary)
                        <div class="mt-4 flex flex-col gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-400">Meses</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($months as $index => $month)
                                        <label>
                                            <input type="checkbox" wire:model.live="form.extraordinary_months" value="{{ $index + 1 }}" class="peer sr-only">
                                            <span class="inline-flex cursor-pointer rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 ring-1 ring-inset ring-gray-300 transition hover:bg-white peer-checked:bg-primary-500 peer-checked:text-white peer-checked:ring-primary-500 dark:text-gray-300 dark:ring-white/20 dark:hover:bg-white/10">{{ $month }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="max-w-xs">
                                <x-wireui-inputs.currency label="Valor de cada cuota extraordinaria" prefix="$" thousands="." decimal="," precision="0" wire:model.live.debounce.600ms="form.extraordinary_amount" />
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Extra payments and edited quotas --}}
                <fieldset class="col-span-6">
                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-400">Cuando haya abonos o se cambie el valor de una cuota</legend>
                    <div class="mt-2 grid gap-3 sm:grid-cols-2">
                        <label class="block">
                            <input type="radio" wire:model.live="form.switch_payment" value="0" class="peer sr-only">
                            <span class="{{ $choice }}">
                                <span class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                    <x-icon name="chevron-double-down" class="h-5 w-5 text-primary-500" /> Reducir el número de cuotas
                                </span>
                                <span class="text-sm">Las siguientes mantienen su valor; se quitan o agregan cuotas al final.</span>
                            </span>
                        </label>
                        <label class="block">
                            <input type="radio" wire:model.live="form.switch_payment" value="1" class="peer sr-only">
                            <span class="{{ $choice }}">
                                <span class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                                    <x-icon name="arrow-trending-down" class="h-5 w-5 text-primary-500" /> Reducir el valor de las cuotas
                                </span>
                                <span class="text-sm">Se mantiene el número de cuotas y el saldo se reparte entre ellas.</span>
                            </span>
                        </label>
                    </div>
                </fieldset>
            </div>
        </section>
    </div>

    {{-- 3. Schedule --}}
    <section class="{{ $card }}">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <x-promise.section-heading number="3" title="Proyección de pagos" description="Revisa y ajusta las cuotas antes de guardar." />

            <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
                @if ($preview)
                    <p class="rounded-xl bg-primary-50 px-4 py-2 text-sm text-primary-900 ring-1 ring-inset ring-primary-200 dark:bg-primary-900/20 dark:text-primary-200 dark:ring-primary-900/40">
                        <strong>{{ count($preview) }}</strong> cuotas
                        @if ($previewOrdinary)
                            de <strong>${{ $money($previewOrdinary['amount']) }}</strong>
                        @endif
                        @unless ($rate->isZero())
                            · intereses ${{ $money(array_sum(array_column($preview, 'interest'))) }}
                        @endunless
                    </p>
                @endif
                <x-wireui-button wire:click="project" spinner="project" color="{{ $isCreate ? 'primary' : 'indigo' }}"
                    :label="$isCreate ? 'Generar proyección' : 'Regenerar cuotas pendientes'" :icon="$isCreate ? 'calculator' : 'arrow-path'" />
            </div>
        </div>

        <div class="mt-6">
            {{ $schedule }}
        </div>
    </section>
</div>
