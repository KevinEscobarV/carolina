<form wire:submit.prevent="save">
    <div class="grid grid-cols-6 gap-6">
        <div class="col-span-6">
            <div class="flex items-center gap-3">
                <div class="border-2 border-indigo-500 rounded-full h-10 w-10 flex items-center justify-center">
                    <span class="bg-clip-text font-bold text-2xl text-transparent bg-indigo-500">
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
            <x-wireui-select label="Manzanas" wire:model.live="block_id"
                placeholder="Seleccione una Manzana" :async-data="route('api.blocks.index')" option-label="code"
                option-value="id" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-select label="Lotes" wire:model.live.debounce.500ms="form.parcels" empty-message="Seleccione una manzana primero"
                :async-data="route('api.blocks.parcels.index', $block_id)"
                placeholder="Marque los lotes"  option-label="number"
                option-value="id" multiselect />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-inputs.currency disabled label="Valor Bienes" placeholder="Suma de valor de Lotes" right-icon="trending-up" prefix="$" thousands="," precision="0" wire:model="form.value" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-datetime-picker label="Fecha de firma" placeholder="Fecha de promesa" wire:model="form.signature_date" without-time />
        </div>
        <div class="col-span-6">
            <div class="flex items-center gap-3 mt-6">
                <div class="border-2 border-gray-500 rounded-full h-10 w-10 flex items-center justify-center">
                    <span class="bg-clip-text font-bold text-2xl text-transparent bg-gray-500">
                        2
                    </span>
                </div>
                <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Acuerdo de pago
                </h2>
            </div>
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-select label="Medio de Pago" placeholder="Seleccione un medio de pago" :options="App\Enums\PromisePaymentMethod::select()"
                option-label="label" option-value="value" wire:model="form.payment_method" autocomplete="off" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-inputs.currency label="Couta Inicial" placeholder="Primer pago" right-icon="trending-up" prefix="$" thousands="," precision="0" wire:model="form.initial_fee" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-inputs.number label="Numero de Cuotas" min="0" placeholder="Cantidad de Cuotas" wire:model="form.number_of_fees"/>
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
            <div class="flex items-center gap-3 mt-6">
                <div class="border-2 border-lime-500 rounded-full h-10 w-10 flex items-center justify-center">
                    <span class="bg-clip-text font-bold text-2xl text-transparent bg-lime-500">
                        3
                    </span>
                </div>
                <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Información de Escritura
                </h2>
            </div>
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-input label="Numero de Folio de Matricula" right-icon="pencil" placeholder="Numero Escritura" wire:model="form.deed_number" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-inputs.currency label="Valor de Escritura" placeholder="Gastos de escrituración" right-icon="trending-up" prefix="$" thousands="," precision="0" wire:model="form.value" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-select label="Estado de Escritura" placeholder="Seleccione un estado" :options="App\Enums\PromiseStatus::select()"
                option-label="label" option-value="value" wire:model="form.status" autocomplete="off" />
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-datetime-picker label="Fecha de escritura" placeholder="Fecha de escritura" wire:model="form.deed_date" without-time />
        </div>
        <div class="col-span-6">
            <div class="flex items-center gap-3">
                <div class="border-2 border-yellow-500 rounded-full h-10 w-10 flex items-center justify-center">
                    <span class="bg-clip-text font-bold text-2xl text-transparent bg-yellow-500">
                        4
                    </span>
                </div>
                <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Observaciones
                </h2>
            </div>
        </div>
        <div class="col-span-6">
            <x-wireui-textarea label="Observaciones" placeholder="Observaciones o concepto"
                wire:model="form.observations" />
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6">
        <x-wireui-button type="submit" spinner="save" lime label="Crear Promesa" />
    </div>
</form>