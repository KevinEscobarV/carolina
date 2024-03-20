<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Configuración
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-card>
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6">
                        <div class="flex items-center gap-3">
                            <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                                <x-icon name="envelope" class="h-6 text-primary-500" />
                            </div>
                            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                Mensaje de Pago en Morosidad
                            </h2>
                        </div>
                    </div>
                    <div class="col-span-6">
                        <x-wireui-textarea label="Mensaje" placeholder="Escriba un mensaje para el pago en morosidad" rows="3"
                            wire:model="message" />
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 mt-6">
                    <x-wireui-button type="submit" spinner="save" primary label="Actualizar Pago" />
                </div>
            </form>
        </x-card>
    </div>
</div>