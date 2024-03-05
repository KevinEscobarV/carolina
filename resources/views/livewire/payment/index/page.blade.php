<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Pagos
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">

    <livewire:payment.index.create />

    <livewire:payment.index.table lazy />

    <livewire:payment.index.edit />

</div>