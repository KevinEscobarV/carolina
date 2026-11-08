<x-report.card icon="map" title="Lotes" description="Los lotes por posición, estado y folio de matrícula.">
    <x-wireui-select label="Posición" placeholder="Todas" :options="App\Enums\ParcelPosition::select()"
        option-label="label" option-value="value" wire:model="position" autocomplete="off" />

    <x-wireui-select label="Estado" placeholder="Todos"
        :options="[['label' => 'Disponible 🟢', 'value' => 'available'], ['label' => 'Vendido 🟡', 'value' => 'sold']]"
        option-label="label" option-value="value" wire:model="status" autocomplete="off" />

    <div class="col-span-2">
        <x-wireui-select label="Folio de matrícula" placeholder="Todos"
            :options="[['label' => 'Con folio de matricula 🟢', 'value' => 'withRegistrationNumber'], ['label' => 'Sin folio de matricula 🟡', 'value' => 'withoutRegistrationNumber']]"
            option-label="label" option-value="value" wire:model="registrationNumber" autocomplete="off" />
    </div>
</x-report.card>
