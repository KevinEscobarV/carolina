<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Gestión de Usuarios
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8" x-data="{ open: true }">
    <livewire:user.index.create />
    <livewire:user.index.table />
</div>