<div class="max-w-4xl">
    <x-card cardClasses="p-2">
        <form wire:submit.prevent="sendSMS">
            <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6">
                    <div class="flex items-center gap-3">
                        <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                            <x-icon name="chat-bubble-left-ellipsis" class="h-6 text-primary-500" />
                        </div>
                        <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Enviar Mensaje
                        </h2>
                    </div>
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-wireui-select label="Clientes" wire:model="buyers"
                        placeholder="Marque los usuarios" :async-data="route('api.buyers.index')" option-label="names"
                        option-value="id" multiselect />
                </div>
                <div class="col-span-6">
                    <x-wireui-textarea label="Mensaje" placeholder="Escriba un mensaje"
                        wire:model="message" />
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button type="submit" spinner="sendSMS" primary label="Enviar Mensaje" />
            </div>
        </form>
    </x-card>
</div>