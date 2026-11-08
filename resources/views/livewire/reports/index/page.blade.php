<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Reportes
    </h2>
</x-slot>

<div class="mx-auto flex flex-col gap-6 sm:px-6 lg:px-8">
    <p class="text-gray-600 dark:text-gray-400">
        Descarga en Excel la información de la campaña <strong class="font-semibold text-gray-800 dark:text-gray-200">{{ auth()->user()->currentCategory?->name }}</strong>.
    </p>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @can('view.promises')
            <livewire:reports.promise.wallet-status />
        @endcan
        @can('view.payments')
            <livewire:reports.payment.general />
        @endcan
        @can('view.promises')
            <livewire:reports.promise.general />
        @endcan
        @can('view.buyers')
            <livewire:reports.buyer.general />
        @endcan
        @can('view.parcels')
            <livewire:reports.parcel.general />
        @endcan
    </div>
</div>
