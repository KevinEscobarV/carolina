<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Registrar Pago
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8 grid grid-cols-12 gap-6">
    <div class="col-span-12 lg:col-span-7">
        <x-card>
            <form wire:submit.prevent="save">
                <x-payment.form />
                <div class="flex items-center justify-end gap-2 mt-6">
                    <x-wireui-button lg rose label="Volver" href="{{ route('payments') }}" icon="rewind" wire:navigate />
                    <x-wireui-button lg type="submit" spinner="save" lime label="Crear Pago" icon="save-as" />
                </div>
            </form>
        </x-card>
    </div>

    <livewire:payment.components.amortization />
</div>
