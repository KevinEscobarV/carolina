<div class="grid grid-cols-6 gap-4">
    <div class="col-span-6">
        <div class="flex items-center gap-3">
            <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                <x-wireui-icon name="user" class="h-6 text-primary-500" />
            </div>
            <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Creación de Comprador
            </h2>
        </div>
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Nombres" placeholder="Nombres del Usuario" wire:model="form.names" />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Apellidos" placeholder="Apellidos del Usuario" wire:model="form.surnames" />
    </div>
    <div class="col-span-6 sm:col-span-4">
        <x-wireui-input label="Correo electronico" placeholder="@example.com" wire:model="form.email" />
    </div>
    <div class="col-span-6 sm:col-span-2">
        <x-wireui-select
            label="Tipo de documento"
            placeholder="Seleccione un tipo de documento"
            :options="App\Enums\DocumentType::select()"
            option-label="label"
            option-value="value"
            wire:model="form.document_type"
            autocomplete="off"
        />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-wireui-input label="Numero Documento" placeholder="1.115.918.673" wire:model="form.document_number" />
    </div>
    <div class="col-span-6 sm:col-span-2">
        <x-wireui-select
            label="Estado Civil"
            placeholder="Seleccione un tipo de documento"
            :options="App\Enums\CivilStatus::select()"
            option-label="label"
            option-value="value"
            wire:model="form.civil_status"
            autocomplete="off"
        />
    </div>
    <div class="col-span-6 sm:col-span-2">
        <x-wireui-inputs.phone label="Telefono" placeholder="(321) 202-8286" wire:model="form.phone_one" />
    </div>
    <div class="col-span-6 sm:col-span-2">
        <x-wireui-inputs.phone label="Telefono Alternativo"  placeholder="(321) 202-8286" wire:model="form.phone_two" />
    </div>
    <div class="col-span-6">
        <x-wireui-input label="Dirección" placeholder="Carrera 34 # 12 - 34" wire:model="form.address" />
    </div>
</div>