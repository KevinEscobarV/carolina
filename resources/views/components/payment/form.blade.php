<div class="grid grid-cols-6 gap-6">
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                <span class="bg-clip-text font-bold text-2xl text-transparent bg-primary-500">
                    1
                </span>
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Seleccione la promesa
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Promesa " wire:model.live="form.promise_id"
            placeholder="Buscar por Promesa o Usuario" :async-data="route('api.promises.index')" option-label="number"
            option-value="id" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-label for="switch_payment" value="Cuota Inicial" class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1" />
        <input type="checkbox" id="reduction" wire:model.live="form.is_initial_fee" value="false" class="hidden peer">
        <label for="reduction" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-primary-500 peer-checked:border-primary-600 peer-checked:text-primary-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
            <div class="block">
                <div class="w-full text-lg font-semibold">Es cuota inicial</div>
                <div class="w-full">Seleccione si el pago es la cuota inicial de la promesa</div>
            </div>
            <x-icon name="lock-closed" class="w-6 h-6" />
        </label>
    </div>
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-gray-500 rounded-full h-10 w-10 flex items-center justify-center">
                <span class="bg-clip-text font-bold text-2xl text-transparent bg-gray-500">
                    2
                </span>
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Información del pago
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-4">
        <x-wireui-input label="Numero de Recibo o Pago" placeholder="N° Recibo" wire:model="form.bill_number" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-datetime-picker label="Fecha pactada" placeholder="Fecha pactada" wire:model="form.agreement_date"
            without-time />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-inputs.currency label="Valor" placeholder="Valor pactado" right-icon="trending-up" prefix="$" thousands=","
            precision="0" wire:model="form.agreement_amount" />
    </div>
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-yellow-500 rounded-full h-10 w-10 flex items-center justify-center">
                <span class="bg-clip-text font-bold text-2xl text-transparent bg-yellow-500">
                    3
                </span>
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Asignar valores de pago
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Medio de Pago" placeholder="Seleccione un medio de pago" :options="App\Enums\PaymentMethod::select()"
            option-label="label" option-value="value" wire:model="form.payment_method" autocomplete="off" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-select label="Banco (Opcional)" placeholder="Seleccione un banco" :options="App\Enums\PaymentBanks::select()"
            option-label="label" option-value="value" wire:model="form.bank" autocomplete="off" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-datetime-picker label="Fecha de pago" placeholder="Fecha de pago" wire:model="form.payment_date"
            without-time />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-inputs.currency label="Valor cancelado" placeholder="Valor cancelado" right-icon="trending-up" prefix="$"
            thousands="," precision="0" wire:model="form.paid_amount" />
    </div>
    <div class="col-span-6">
        <x-wireui-textarea label="Observaciones" placeholder="Observaciones o concepto"
            wire:model="form.observations" />
    </div>
</div>