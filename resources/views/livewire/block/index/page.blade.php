<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Manzanas
    </h2>
</x-slot>

<div class="container mx-auto sm:px-6 lg:px-8">
    <div class="flex flex-col gap-8">
        <x-card cardClasses="p-2 max-w-3xl">
            <livewire:block.index.create />
        </x-card>

        <livewire:block.index.table lazy />
    </div>
</div>
