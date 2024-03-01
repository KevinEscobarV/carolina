<form wire:submit.prevent="save">
    <div class="grid grid-cols-6 gap-6">
        <div class="col-span-6">
            <div class="flex items-center gap-3">
                <div class="border-2 border-lime-500 rounded-full h-10 w-10 flex items-center justify-center">
                    <span class="bg-clip-text font-bold text-2xl text-transparent bg-lime-500">
                        1
                    </span>
                </div>
                <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Seleccione la promesa o escritura
                </h2>
            </div>
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-select label="Promesa o escritura" wire:model="form.promise_id"
                placeholder="Seleccione una promesa o escritura" :async-data="route('api.promises.index')" option-label="deed_number"
                option-value="id" />
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
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-datetime-picker label="Fecha pactada" placeholder="Fecha pactada" wire:model="form.agreement_date"
                without-time />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-inputs.currency label="Valor" placeholder="Valor pactado" right-icon="trending-up" prefix="$" thousands=","
                precision="0" wire:model="form.amount" />
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
            <x-wireui-select label="Medio de Pago" placeholder="Seleccione un medio de documento" :options="App\Enums\PaymentMethod::select()"
                option-label="label" option-value="value" wire:model="form.payment_method" autocomplete="off" />
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
    <div class="flex items-center justify-end gap-2 mt-6">
        <x-wireui-button type="submit" spinner="save" lime label="Crear Pago" />
    </div>
</form>
