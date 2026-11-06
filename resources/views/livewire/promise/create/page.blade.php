<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Crear Promesa
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8">
    <form wire:submit="save" class="flex flex-col gap-6">
        <x-promise.form :$block :$isCreate :$form>
            <x-slot:schedule>
                <x-promise.schedule-editor
                    :rows="$schedule"
                    :version="$scheduleVersion"
                    :principal="$form->scheduleTerms()->principal"
                    :has-interest="(float) $form->interest_rate > 0"
                    :mode="App\Enums\RebalanceMode::fromSwitchPayment((bool) $form->switch_payment)" />
            </x-slot:schedule>
        </x-promise.form>

        <div class="sticky bottom-4 z-20 flex items-center justify-end gap-2 rounded-2xl bg-white/90 p-3 shadow-lg ring-1 ring-gray-950/5 backdrop-blur dark:bg-gray-800/90 dark:ring-white/10">
            <x-wireui-button rose label="Volver" href="{{ route('promises') }}" icon="arrow-uturn-left" />
            <x-wireui-button type="submit" spinner="save" lime label="Crear promesa" icon="check" />
        </div>
    </form>
</div>
