<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Compradores
    </h2>
</x-slot>

<div class="container mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">
    <x-card title="Crear Usuario" cardClasses="max-w-4xl">
        <x-slot name="action">
            <x-wireui-icon name="plus" class="w-6 h-6 text-gray-500" />
        </x-slot>
        <livewire:buyer.index.create />
    </x-card>
    <x-card title="Tabla de Usuarios">
        <x-slot name="action">
            <x-wireui-icon name="table" class="w-6 h-6 text-gray-500" />
        </x-slot>
        <livewire:buyer.index.table lazy />
    </x-card>
</div>