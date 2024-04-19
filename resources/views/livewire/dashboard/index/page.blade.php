<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Panel administrativo
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8 flex flex-col gap-6">
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 sm:col-span-4">
            <div class="rounded-xl h-full bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10 overflow-hidden">
                <livewire:dashboard.index.doughnut :$filters lazy />
            </div>
        </div>
        <div class="col-span-12 sm:col-span-8">
            <div class="rounded-xl h-full flex flex-col bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10 overflow-hidden">
                <div class="gap-4 flex flex-col items-start justify-start sm:flex-row sm:justify-between sm:items-center px-6 pt-6">
                    <div class="flex flex-col gap-1">
                        <h1 class="text-3xl text-gray-800 dark:text-gray-200">Pagos</h1>
                    </div>
                    <div class="flex gap-2">
                        <x-payment.filter-range :$filters />
                    </div>
                </div>
                <div class="flex flex-col gap-4 p-6">
                    <x-payment.filter-status :$filters />
                </div>
                <div class="mt-auto">
                    <livewire:dashboard.index.chart :$filters lazy />
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-800 dark:ring-white/10">
        <div class="px-6 pt-6">
            <h1 class="text-3xl text-gray-800 dark:text-gray-200">Reportes</h1>
        </div>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 p-6">
            <livewire:reports.promise.general />
            <livewire:reports.buyer.general />
            <livewire:reports.parcel.general />
            <livewire:reports.payment.general />
            <livewire:reports.promise.wallet-status />
        </div>
    </div>
</div>

@assets
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets