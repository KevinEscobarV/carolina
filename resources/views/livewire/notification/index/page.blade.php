<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Notificaciones
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8 grid grid-cols-12 gap-8">
    <livewire:notification.index.indicators />
    <livewire:notification.index.overdue />
    <livewire:notification.index.s-m-s />
</div>
