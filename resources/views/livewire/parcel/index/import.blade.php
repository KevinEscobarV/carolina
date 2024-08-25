<div>
    <x-wireui-button class="h-full" icon="upload" label="Importar Lotes" sky wire:click="$toggle('openImport')" />

    <x-modal wire:model="openImport">
        <div class="max-w-4xl">
            <x-card>
                <form wire:submit.prevent="import">
                    <div x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-cancel="uploading = false"
                        x-on:livewire-upload-error="uploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <div class="w-full">
                            <x-label for="file" value="Importar Lotes" />
                            <p class="text-sm text-gray-500">Las manzanas no existentes serán creadas automáticamente.</p>
                            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 dark:border-gray-600 px-6 py-10">
                                <div class="flex items-center">
                                    <div class="shrink-0">
                                        <img class="w-12" src="{{ asset('img/sheets.png') }}"
                                            alt="Current_file_photo">
                                    </div>
                                    <label class="block ml-4">
                                        <span class="sr-only">Elegir Archivo</span>
                                        <input type="file" wire:model="file" accept=".xlsx, .xls, .csv"
                                            class="block w-full text-sm text-gray-300 file:text-sm file:font-semibold file:py-2 file:px-4 file:bg-violet-50 file:text-green-700 file:rounded-full file:border-0 file:mr-4 hover:file:bg-green-100">
                                            
                                        <x-wireui-error name="file" />
                                    </label>
                                </div>
                            </div>
                            <div class="col-span-2">
                                <x-label for="update_existing" value="Actualizar lotes existentes" class="mt-2" />
                                <p class="text-sm text-gray-500">Si marca esta opción, los lotes existentes se actualizarán con los datos del archivo.</p>
                                <x-wireui-toggle wire:model="update_existing" lg />
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full" x-show="uploading">
                            <progress class="w-full rounded-lg overflow-hidden" max="100" x-bind:value="progress"></progress>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 mt-6">
                        <x-wireui-button lime label="Descargar plantilla" wire:click="downloadTemplate" />
                        <x-wireui-button gray label="Volver" x-on:click="show = false" />
                        <x-wireui-button type="submit" spinner="update" primary spinner="import" label="Importar datos" />
                    </div>
                </form>
            </x-card>
        </div>
    </x-modal>
</div>