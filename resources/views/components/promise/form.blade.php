<div class="grid grid-cols-2 gap-6">
    <div class="col-span-2 lg:col-span-1">
        <div class="grid grid-cols-6 gap-6 bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-900/40 rounded-lg p-3">
            <div class="col-span-6">
                <div class="flex items-center gap-3">
                    <div class="border-2 border-orange-500 rounded-full h-10 w-10 flex items-center justify-center">
                        <span class="bg-clip-text font-bold text-2xl text-transparent bg-orange-500">
                            1
                        </span>
                    </div>
                    <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Bienes y Usuarios Asociados
                    </h2>
                </div>
            </div>
            <div class="col-span-6 sm:col-span-4">
                <x-wireui-select label="Clientes" wire:model="form.buyers"
                    placeholder="Marque los usuarios" :async-data="route('api.buyers.index')" option-label="names"
                    option-value="id" multiselect />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-select label="Manzanas" wire:model.live="block"
                    placeholder="Seleccione una Manzana" :async-data="route('api.blocks.index')" option-label="code"
                    option-value="id" />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-select label="Lotes" wire:model.live.debounce.500ms="form.parcels" empty-message="Seleccione una manzana primero"
                    :async-data="route('api.parcels.promises', $block)"
                    placeholder="Marque los lotes"  option-label="number"
                    option-value="id" multiselect />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-inputs.currency label="Valor Bienes" placeholder="Suma de valor de Lotes" right-icon="trending-up" prefix="$" thousands="," precision="0" wire:model="form.value" />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-datetime-picker label="Fecha de firma" placeholder="Fecha de promesa" wire:model="form.signature_date" without-time />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-datetime-picker label="Fecha de firma Escritura" placeholder="Fecha de escritura" wire:model="form.signature_deed_date" without-time />
            </div>
        </div>
    </div>
    <div class="col-span-2 lg:col-span-1">
        <div class="grid grid-cols-6 gap-6 bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-900/40 rounded-lg p-3">
            <div class="col-span-6">
                <div class="flex items-center gap-3">
                    <div class="border-2 border-indigo-500 rounded-full h-10 w-10 flex items-center justify-center">
                        <span class="bg-clip-text font-bold text-2xl text-transparent bg-indigo-500">
                            2
                        </span>
                    </div>
                    <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Acuerdo de pago
                    </h2>
                </div>
            </div>
            <div class="col-span-6 sm:col-span-4">
                <x-wireui-input label="Numero o Codigo de Promesa" placeholder="Identificativo Promesa" wire:model="form.number" />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-select label="Medio de Pago" placeholder="Seleccione un medio de pago" :options="App\Enums\PromisePaymentMethod::select()"
                    option-label="label" option-value="value" wire:model="form.payment_method" autocomplete="off" />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-inputs.currency label="Couta Inicial" placeholder="Primer pago" right-icon="trending-up" prefix="$" thousands="," precision="0" wire:model="form.initial_fee" />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-inputs.currency label="Valor Cuota" placeholder="Valor Cuota" right-icon="trending-up" prefix="$" thousands="," precision="0" wire:model="form.quota_amount" />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-inputs.number label="Tasa de Interés (Opcional)" placeholder="Tasa de Interes %" wire:model="form.interest_rate"/>
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-datetime-picker label="Primera Fecha de Corte" placeholder="Fecha de corte" wire:model="form.cut_off_date" without-time />
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-select label="Periodicidad de Pago" placeholder="Seleccione una frecuencia" :options="App\Enums\PaymentFrequency::select()"
                    option-label="label" option-value="value" wire:model="form.payment_frequency" autocomplete="off" />
            </div>
            <div class="col-span-6">
                <x-label for="switch_payment" value="¿Como desea que se apliquen los abonos adicionales?" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" />
                <ul class="grid w-full gap-6 md:grid-cols-2">
                    <li>
                        <input type="radio" id="reduction" wire:model="form.switch_payment" value="false" class="hidden peer">
                        <label for="reduction" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-indigo-500 peer-checked:border-indigo-600 peer-checked:text-indigo-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">En número de Cuotas</div>
                                <div class="w-full">Reducción de número de cuotas</div>
                            </div>
                            <x-icon name="chevron-double-down" class="w-6 h-6" />
                        </label>
                    </li>
                    <li>
                        <input type="radio" id="decrease" wire:model="form.switch_payment" value="true" class="hidden peer" required />
                        <label for="decrease" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-indigo-500 peer-checked:border-indigo-600 peer-checked:text-indigo-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">                           
                            <div class="block">
                                <div class="w-full text-lg font-semibold">En la Cuota</div>
                                <div class="w-full">Disminución del valor de cuotas</div>
                            </div>
                            <x-icon name="arrow-trending-down" class="w-6 h-6" />
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-span-2">
        <div class="grid grid-cols-6 gap-6 bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-gray-900/40 rounded-lg p-3">
            <div class="col-span-6">
                <div class="flex items-center gap-3">
                    <div class="border-2 border-yellow-500 rounded-full h-10 w-10 flex items-center justify-center">
                        <span class="bg-clip-text font-bold text-2xl text-transparent bg-yellow-500">
                            3
                        </span>
                    </div>
                    <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Observaciones
                    </h2>
                </div>
            </div>
            <div class="col-span-6 sm:col-span-3">
                <x-wireui-select label="Estado de Promesa" placeholder="Seleccione un estado" :options="App\Enums\PromiseStatus::select()"
                    option-label="label" option-value="value" wire:model="form.status" autocomplete="off" />
            </div>
            <div class="col-span-6">
                <x-wireui-textarea label="Observaciones" placeholder="Observaciones o concepto"
                    wire:model="form.observations" />
            </div>
        </div>
    </div>
</div>