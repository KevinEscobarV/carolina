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

    <x-card cardClasses="mt-6 p-3">
        <form wire:submit="saveArrears">
            <div class="flex items-center gap-3">
                <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                    <x-icon name="scale" class="h-6 text-primary-500" />
                </div>
                <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Mora y tasa de usura
                </h2>
            </div>

            <div class="mt-6 grid gap-6 md:grid-cols-2">
                <div class="flex flex-col gap-2">
                    <x-wireui-inputs.number label="Días de gracia (campaña actual)" wire:model="grace_days" min="0" max="90" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Días después del vencimiento antes de que una cuota cuente como mora y aparezca en Notificaciones.
                        Con 0, la mora empieza el día siguiente al vencimiento.
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <x-wireui-inputs.number label="Tasa de usura vigente (% efectivo anual)" wire:model="usury_rate" step="0.01" min="0" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Interés máximo legal; aplica a todas las campañas. La certifica la
                        <a href="https://www.superfinanciera.gov.co/publicaciones/10116308/superfinanciera-certifica-el-interes-bancario-corriente/" target="_blank" rel="noopener" class="font-medium text-primary-600 hover:underline dark:text-primary-400">Superintendencia Financiera</a>
                        cada mes (crédito de consumo y ordinario). Se avisa al crear una promesa con una tasa mayor.
                    </p>
                    <p @class([
                        'flex items-center gap-2 rounded-lg px-3 py-2 text-sm',
                        'bg-amber-50 text-amber-800 ring-1 ring-inset ring-amber-200 dark:bg-amber-900/20 dark:text-amber-200 dark:ring-amber-900/50' => $usuryIsStale,
                        'bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-200 dark:bg-white/5 dark:text-gray-300 dark:ring-white/10' => ! $usuryIsStale,
                    ])>
                        <x-icon :name="$usuryIsStale ? 'exclamation-triangle' : 'clock'" class="h-4 w-4 shrink-0" />
                        Revisada por última vez el {{ $usuryCheckedAt->translatedFormat('d \d\e F \d\e Y') }}{{ $usuryIsStale ? ': conviene actualizarla.' : '.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button type="submit" spinner="saveArrears" primary label="Guardar" />
            </div>
        </form>
    </x-card>
</div>