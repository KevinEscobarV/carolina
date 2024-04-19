<div class="col-span-12 lg:col-span-5">
    <x-card>
        @if ($promise)
            <div class="flex flex-wrap -m-3 mb-3">
                <div class="px-3 pt-3 w-full">
                    <div class="flex items-center gap-3">
                        <h2 class="text-lg text-gray-800 dark:text-gray-200 leading-tight">
                            {{ $promise->number}}
                        </h2>
                        <div wire:loading class="flex items-start ml-auto">
                            <svg class="animate-spin h-9 w-9 dark:text-secondary-300" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-3 md:w-1/2">
                    <div class="flex rounded-lg h-full bg-gray-100 dark:bg-gray-900/30 p-8 flex-col">
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 mr-3 inline-flex items-center justify-center rounded-full bg-primary-500 dark:bg-primary-500 text-white flex-shrink-0">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" class="w-5 h-5"
                                    viewBox="0 0 24 24">
                                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                </svg>
                            </div>
                            <h2 class="text-gray-900 dark:text-gray-200 text-lg title-font font-medium">Total Promesa</h2>
                        </div>
                        <div class="flex-grow">
                            <p class="font-light text-lg">
                                <span class="text-gray-400 dark:text-gray-300">$</span> {{ $promise->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="p-3 md:w-1/2">
                    <div class="flex rounded-lg h-full bg-gray-100 dark:bg-gray-900/30 p-8 flex-col">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-8 h-8 mr-3 inline-flex items-center justify-center rounded-full bg-primary-500 dark:bg-primary-500 text-white flex-shrink-0">
                                <svg fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" class="w-5 h-5"
                                    viewBox="0 0 24 24">
                                    <circle cx="6" cy="6" r="3"></circle>
                                    <circle cx="6" cy="18" r="3"></circle>
                                    <path d="M20 4L8.12 15.88M14.47 14.48L20 20M8.12 8.12L12 12"></path>
                                </svg>
                            </div>
                            <h2 class="text-gray-900 dark:text-gray-200 text-lg title-font font-medium">Total Pagado</h2>
                        </div>
                        <div class="flex-grow">
                            <p class="font-light text-lg">
                                <span class="text-gray-400 dark:text-gray-300">$</span> {{ $promise->total_paid_formatted }} <span class="text-gray-400 text-sm">COP</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <form wire:submit.prevent="save">
                @if ($promise->projection)
                    <div class="col-span-6">
                        @if ($promise->interest_rate == 0)
                            <x-payment.edit-projection :$projection />
                        @else
                            <x-promise.projection :$projection />
                        @endif
                    </div>
                @endif
                <div class="flex items-center justify-end gap-2 mt-6 p-2">
                    <x-wireui-button type="submit" spinner="save" primary label="Actualizar Proyección" />
                </div>
            </form>
        @else
            <div class="p-3 w-full">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg text-gray-800 dark:text-gray-200 leading-tight">
                        Selecciona una promesa
                    </h2>
                    <div wire:loading class="flex items-start ml-auto">
                        <svg class="animate-spin h-9 w-9 dark:text-secondary-300" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        @endif
    </x-card>
</div>
