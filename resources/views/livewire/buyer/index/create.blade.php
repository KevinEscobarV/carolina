<form wire:submit.prevent="save">
    <div class="grid grid-cols-6 gap-4">
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
        <div class="col-span-6 sm:col-span-3">
            <x-wireui-input label="Dirección" placeholder="Carrera 34 # 12 - 34" wire:model="form.address" />
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 mt-6">
        <x-wireui-button type="submit" spinner="save" lime label="Guardar" />
    </div>
</form>