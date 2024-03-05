<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Lotes
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8">
    <div class="flex flex-col gap-8">
        <livewire:parcel.index.create />
        <livewire:parcel.index.table lazy />
    </div>
</div>
