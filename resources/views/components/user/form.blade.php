<div class="grid grid-cols-6 gap-4">
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                <x-wireui-icon name="user" class="h-6 text-primary-500" />
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Creación de Usuario
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Nombre" placeholder="Nombre del Usuario" wire:model="form.name" />
    </div>
    <div class="col-span-6 sm:col-span-4">
        <x-wireui-input label="Correo electronico" placeholder="@example.com" wire:model="form.email" />
    </div>
    <div class="col-span-6 sm:col-span-2">
        <x-wireui-select
            label="Roles"
            placeholder="Seleccione roles a asignar"
            :options="$roles"
            option-label="name"
            option-value="id"
            wire:model="form.roles"
            autocomplete="off"
            multiselect
        />
    </div>
    <div class="col-span-6 sm:col-span-2">
        <x-wireui-select
            label="Campaña"
            placeholder="Seleccione una campaña"
            :options="$categories"
            option-label="name"
            option-value="id"
            wire:model="form.current_category_id"
            autocomplete="off"
        />
    </div>
    <div class="col-span-6 mt-5">
        <div class="flex items-center gap-3">
            <div class="border-2 border-orange-500 rounded-full h-10 w-10 flex items-center justify-center">
                <x-wireui-icon name="lock-closed" class="h-6 text-orange-500" />
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Asignación de Contraseña
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-inputs.password label="Contraseña" placeholder="Contraseña" wire:model="form.password" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-inputs.password label="Confirmar Contraseña" placeholder="Repite la contraseña" wire:model="form.password_confirmation" />
    </div>
</div>