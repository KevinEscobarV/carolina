<div class="max-w-4xl" x-show="open" :class="{ 'pb-8': open }" x-transition>
    <x-card>
        <form wire:submit.prevent="save">
            <x-payment.form />
            <div class="flex items-center justify-end gap-2 mt-6 p-2">
                <x-wireui-button type="submit" spinner="save" primary label="Crear Pago" />
            </div>
        </form>
    </x-card>
</div>
