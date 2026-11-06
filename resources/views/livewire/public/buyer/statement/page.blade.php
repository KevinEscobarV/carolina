<section class="relative isolate min-h-[80svh] overflow-hidden bg-stone-950 px-4 pb-24 pt-36 sm:px-6">
    <img src="{{ asset('img/home/hero.webp') }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-50">
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-stone-950/80 via-stone-950/70 to-stone-950"></div>

    <div class="mx-auto max-w-2xl">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-amber-300">Clientes</p>
            <h1 class="mt-2 font-display text-4xl font-extrabold tracking-tight text-white sm:text-5xl">Consulta tu estado de cuenta</h1>
            <p class="mx-auto mt-4 max-w-lg text-lg text-stone-300">Escribe tu número de documento para ver tus promesas y descargar tu estado de cuenta en PDF.</p>
        </div>

        <div class="mt-10 rounded-3xl bg-white p-6 shadow-2xl sm:p-8">
            <p class="text-lg font-semibold text-stone-900">Número de documento</p>
            <p class="mt-1 text-sm text-stone-500">La cédula o el NIT con el que firmaste la promesa de compraventa.</p>
            <div class="mt-5">
                <livewire:public.buyer.statement.lookup />
            </div>
        </div>

        <p class="mt-8 text-center text-sm text-stone-400">
            ¿Necesitas ayuda? <a href="{{ route('home') }}#contacto" class="font-semibold text-amber-300 hover:text-amber-200">Contáctanos</a>
        </p>
    </div>
</section>
