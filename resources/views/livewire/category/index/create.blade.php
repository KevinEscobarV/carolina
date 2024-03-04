<form wire:submit.prevent="save">
    <div class="grid grid-cols-6 gap-6">
        <div class="col-span-6">
            <div class="flex items-center gap-3">
                <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                    <x-wireui-icon name="speakerphone" class="h-6 text-primary-500" />
                </div>
                <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Creación de Campaña
                </h2>
            </div>
        </div>
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-input label="Nombre de la Campaña" right-icon="pencil" placeholder="Rivarca" wire:model="form.name" />
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6">
        <x-wireui-button type="submit" spinner="save" primary label="Crear Campaña" />
    </div>
</form>