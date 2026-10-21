<div class="max-w-4xl" x-show="open" :class="{ 'pb-8': open }" x-transition>
    <x-card>
        <form wire:submit.prevent="save">
            <x-buyer.form>
                @can('change.category')
                    <div class="col-span-6">
                        <x-wireui-select
                            label="Copiar datos de otra campaña (opcional)"
                            placeholder="Busque por nombre, documento o correo"
                            :async-data="route('api.buyers.other-categories')"
                            option-label="names"
                            option-value="id"
                            wire:model.live="copyFrom"
                        />
                    </div>
                @endcan
            </x-buyer.form>
            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button type="submit" spinner="save" primary label="Guardar" />
            </div>
        </form>
    </x-card>
</div>