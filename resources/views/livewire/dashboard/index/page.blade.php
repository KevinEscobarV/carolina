<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Panel administrativo
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8 flex flex-col gap-6">
    <div class="col-span-12">
        <div class="flex flex-col text-center w-full mb-6">
            <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-700 dark:text-gray-300">
                Indicadores
            </h1>
            <p class="lg:w-2/3 mx-auto leading-relaxed text-base text-gray-700 dark:text-gray-300">
                A continuación se muestran los indicadores de proyección de ventas vs recibidos.
            </p>
        </div>
        <div class="flex flex-wrap -m-4 text-center">
            <div class="p-4 sm:w-1/2 w-full">
                <div class="border-2 border-sky-500 bg-white dark:bg-gray-800 px-4 py-6 rounded-xl">
                    <x-icon name="arrow-trending-up" class="text-sky-500 w-12 h-12 mb-3 inline-block" />
                    <h2 class="title-font font-medium text-3xl dark:text-white text-gray-900"><span
                        class="text-gray-400">$</span> {{ $projection }} <span
                        class="text-gray-400 text-sm">COP</span></h2>
                    <p class="leading-relaxed dark:text-gray-300 text-gray-600">Proyección</p>
                    
                </div>
            </div>
            <div class="p-4 sm:w-1/2 w-full">
                <div class="border-2 border-lime-500 bg-white dark:bg-gray-800 px-4 py-6 rounded-xl">
                    <x-icon name="chart-pie" class="text-lime-500 w-12 h-12 mb-3 inline-block" />
                    <h2 class="title-font font-medium text-3xl dark:text-white text-gray-900"><span
                            class="text-gray-400">$</span> {{ $total }} <span
                            class="text-gray-400 text-sm">COP</span></h2>
                    <p class="leading-relaxed dark:text-gray-300 text-gray-600">Ventas</p>
                </div>
            </div>
        </div>
    </div>
    
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