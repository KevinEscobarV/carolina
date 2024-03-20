<div class="animate-pulse">
    <div class="flex flex-col text-center w-full mb-6">
        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-700 dark:text-gray-300">
            Indicadores de notificaciones
        </h1>
        <p class="lg:w-2/3 mx-auto leading-relaxed text-base text-gray-700 dark:text-gray-300">
            A continuación se muestran los indicadores de notificaciones, en donde se puede observar el total de creditos, el valor de cada credito y el total de mensajes enviados.
        </p>
    </div>
    <div class="flex flex-wrap -m-4 text-center">
        <div class="p-4 md:w-1/3 sm:w-1/2 w-full">
            <div class="border-2 border-primary-500 bg-white dark:bg-gray-800 px-4 py-6 rounded-xl">
                <x-icon name="phone-arrow-up-right" class="text-primary-500 w-12 h-12 mb-3 inline-block" />
                <h2 class="title-font font-medium text-3xl dark:text-white text-gray-900">Cargando...</h2>
                <p class="leading-relaxed dark:text-gray-300 text-gray-600">Creditos</p>
            </div>
        </div>
        <div class="p-4 md:w-1/3 sm:w-1/2 w-full">
            <div class="border-2 border-lime-500 bg-white dark:bg-gray-800 px-4 py-6 rounded-xl">
                <x-icon name="arrow-trending-up" class="text-lime-500 w-12 h-12 mb-3 inline-block" />
                <h2 class="title-font font-medium text-3xl dark:text-white text-gray-900"><span class="text-gray-400">$</span> Cargando... <span class="text-gray-400 text-sm">COP</span></h2>
                <p class="leading-relaxed dark:text-gray-300 text-gray-600">Valor credito</p>
            </div>
        </div>
        <div class="p-4 md:w-1/3 sm:w-1/2 w-full">
            <div class="border-2 border-indigo-500 bg-white dark:bg-gray-800 px-4 py-6 rounded-xl">
                <x-icon name="chat-bubble-left-ellipsis" class="text-indigo-500 w-12 h-12 mb-3 inline-block" />
                <h2 class="title-font font-medium text-3xl dark:text-white text-gray-900">Cargando...</h2>
                <p class="leading-relaxed dark:text-gray-300 text-gray-600">Mensajes enviados</p>
            </div>
        </div>
    </div>
</div>
