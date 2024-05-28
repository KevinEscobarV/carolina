<div x-show="open" :class="{ 'pb-8': open }" x-transition>
    <x-card>
        <form wire:submit.prevent="save">
            <x-permission.form :$permissions />
            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button type="submit" spinner="save" primary label="Crear Rol" />
            </div>
        </form>
    </x-card>
</div>