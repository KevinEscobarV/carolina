<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Escrituras
    </h2>
</x-slot>

<div class="container mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">
    <x-card cardClasses="max-w-4xl p-2">
        <livewire:deed.index.create />
    </x-card>

    <livewire:deed.index.table lazy />

</div>