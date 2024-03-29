<div class="max-w-4xl" x-show="open" :class="{ 'pb-8': open }" x-transition>
    <x-card>
        <form wire:submit.prevent="save">
            <x-block.form />
            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button type="submit" spinner="save" lime label="Crear Manzana" />
            </div>
        </form>
    </x-card>
</div>