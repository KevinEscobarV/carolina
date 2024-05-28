<div class="grid grid-cols-6 gap-4">
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                <x-wireui-icon name="user" class="h-6 text-primary-500" />
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Creación de Rol
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Nombre" placeholder="Nombre del Usuario" wire:model="form.name" />
    </div>
    <div class="col-span-6 mt-5">
        <div class="flex items-center gap-3">
            <div class="border-2 border-orange-500 rounded-full h-10 w-10 flex items-center justify-center">
                <x-wireui-icon name="lock-closed" class="h-6 text-orange-500" />
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Asignación de Permisos
            </h2>
        </div>
    </div>
    <div class="col-span-6">
        <div class="grid grid-cols-4 sm:grid-cols-4 gap-4 mt-5">
            @foreach ($permissions as $permission)
                <div>
                    <x-wireui-checkbox label="{{ $permission->description }}" value="{{ $permission->name }}" wire:model="form.permissions" id="permission-{{ $permission->id }}" />
                </div>
            @endforeach
        </div>
    </div>
</div>