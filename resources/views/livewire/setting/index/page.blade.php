<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Configuración
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8" x-data>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <x-card cardClasses="p-3">
            <form wire:submit.prevent="save">
                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6">
                        <div class="flex items-center gap-3">
                            <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                                <x-icon name="envelope" class="h-6 text-primary-500" />
                            </div>
                            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                                SMS de Pago en Morosidad Automatico
                            </h2>
                        </div>
                    </div>
                    <div class="col-span-6">
                        <x-wireui-textarea label="Encabezado" placeholder="Escriba un mensaje para el pago en morosidad"
                            wire:model="message_header" />
                    </div>
                    
                    <div class="col-span-6">
                        <x-wireui-textarea label="Pie de Mensaje" placeholder="Escriba un mensaje para el pago en morosidad"
                            wire:model="message_footer" />
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 mt-6">
                    <x-wireui-button type="submit" spinner="save" primary label="Actualizar Mensaje" />
                </div>
            </form>
        </x-card>
        <div class="md:col-span-1 flex justify-between">
            <div class="px-4 sm:px-0">
                <div class="bg-white dark:bg-gray-800 border-2 border-primary-500 rounded-lg shadow p-3 text-gray-600 dark:text-gray-300 max-w-2xl">
                    <div class="bg-gray-100 dark:bg-black/20 border-2 border-primary-500 rounded-lg p-4">
                        <p>
                            <p x-text="$wire.message_header"></p>
                            Fecha: 02/04/2024, <br>
                            Días mora: 14 <br>
                            <p x-text="$wire.message_footer"></p>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>