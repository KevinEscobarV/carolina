<x-slot:header>
    <h2 class="text-3xl text-gray-700 dark:text-gray-200 leading-tight">
        Editar Escritura {{ $deed->number }}
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8 flex flex-col gap-8">
    <x-card cardClasses="max-w-4xl p-2">
        <form wire:submit.prevent="save">
            <x-deed.form :$block />
            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button rose outline label="Volver" href="{{ route('deeds') }}" wire:navigate />
                <x-wireui-button type="submit" spinner="save" primary label="Actualizar Escritura" />
            </div>
        </form>
    </x-card>
</div>