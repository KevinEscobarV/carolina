<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Promesas
    </h2>
</x-slot>

<div class="mx-auto flex flex-col gap-4 sm:px-6 lg:px-8">
    @can('edit.promises')
        <div class="flex justify-end">
            <x-wireui-button href="{{ route('promises.review') }}" outline primary icon="clipboard-document-check" label="Revisar proyecciones" />
        </div>
    @endcan
    <livewire:promise.index.table />
</div>
