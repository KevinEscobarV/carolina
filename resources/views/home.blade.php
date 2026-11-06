@php
    $whatsapp = fn (string $message) => 'https://wa.me/'.$company['whatsapp'].'?text='.rawurlencode($message);
    $number = fn (int $value) => number_format($value, 0, ',', '.');

    $statuses = [
        'on_sale' => ['label' => 'En venta', 'class' => 'bg-emerald-500 text-white'],
        'few_left' => ['label' => 'Últimos lotes', 'class' => 'bg-amber-400 text-stone-900'],
        'sold_out' => ['label' => 'Vendido', 'class' => 'bg-stone-800 text-white'],
        'soon' => ['label' => 'Próximamente', 'class' => 'bg-white text-stone-900'],
    ];

    $figures = [
        ['value' => $number($stats['projects']), 'label' => 'proyectos en Yopal'],
        ['value' => $number($stats['lots']), 'label' => 'lotes en nuestros proyectos'],
        ['value' => $number($stats['families']), 'label' => 'familias con su lote'],
        ['value' => $number($stats['square_meters']), 'label' => 'm² de terreno'],
    ];

    $benefits = [
        ['icon' => 'banknotes', 'title' => 'Financiación directa', 'text' => 'Paga tu lote en cuotas con un plan a tu medida, sin trámites bancarios.'],
        ['icon' => 'document-check', 'title' => 'Escritura de tu lote', 'text' => 'Te acompañamos en cada paso hasta que la escritura esté a tu nombre.'],
        ['icon' => 'computer-desktop', 'title' => 'Tu cuenta en línea', 'text' => 'Consulta tus pagos y descarga tu estado de cuenta cuando quieras.'],
        ['icon' => 'chart-bar', 'title' => 'Tierra que se valoriza', 'text' => 'Proyectos en zonas de crecimiento de Yopal, una inversión segura para tu familia.'],
    ];

    $steps = [
        ['title' => 'Elige tu lote', 'text' => 'Te asesoramos para encontrar el lote ideal según tu presupuesto. Puedes visitarlo cuando quieras.'],
        ['title' => 'Firma la promesa', 'text' => 'Formalizamos la compra con una promesa de compraventa clara, con el plan de pagos que acordamos.'],
        ['title' => 'Paga a tu ritmo', 'text' => 'Cancela la cuota inicial y el saldo en cuotas, y sigue tus pagos en línea.'],
        ['title' => 'Recibe tu escritura', 'text' => 'Al completar el pago, te acompañamos en la escrituración de tu lote.'],
    ];

    $mapProjects = collect($projects)
        ->filter(fn (array $project) => $project['coordinates'])
        ->map(fn (array $project) => [
            'name' => $project['name'],
            'status' => $statuses[$project['status']]['label'],
            'lat' => $project['coordinates'][0],
            'lng' => $project['coordinates'][1],
        ])
        ->values();
@endphp

<x-public-layout>
    @push('head')
        <link rel="preload" as="image" href="{{ asset('img/home/hero.webp') }}" fetchpriority="high">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'RealEstateAgent',
                'name' => $company['name'],
                'url' => route('home'),
                'image' => asset('img/home/og.jpg'),
                'logo' => asset('apple-touch-icon.png'),
                'telephone' => $company['phone'],
                'email' => $company['email'],
                'areaServed' => "{$company['city']}, {$company['region']}",
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $company['city'],
                    'addressRegion' => $company['region'],
                    'addressCountry' => 'CO',
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}
        </script>
    @endpush

    {{-- Hero --}}
    <section class="relative isolate flex min-h-[100svh] items-center overflow-hidden bg-stone-950 pb-40 pt-32">
        <img src="{{ asset('img/home/hero.webp') }}" alt="" fetchpriority="high" class="absolute inset-0 -z-10 h-full w-full object-cover object-[70%_center]">
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-stone-950/85 via-stone-950/40 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-56 bg-gradient-to-t from-stone-950/70 to-transparent"></div>

        <div class="mx-auto w-full max-w-7xl px-6 lg:px-8">
            <div class="max-w-2xl">
                <x-reveal>
                    <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-sm font-medium text-amber-200 ring-1 ring-inset ring-white/20 backdrop-blur">
                        <x-icon name="map-pin" class="h-4 w-4" />
                        {{ $company['city'] }}, {{ $company['region'] }}
                    </p>
                </x-reveal>
                <x-reveal delay="100">
                    <h1 class="mt-6 font-display text-5xl font-extrabold leading-[1.05] tracking-tight text-white sm:text-6xl lg:text-7xl">
                        Tu lote propio en el
                        <span class="bg-gradient-to-r from-amber-300 via-orange-400 to-orange-500 bg-clip-text text-transparent">corazón del Llano</span>
                    </h1>
                </x-reveal>
                <x-reveal delay="200">
                    <p class="mt-6 max-w-xl text-lg leading-relaxed text-stone-200 sm:text-xl">
                        Lotes en {{ $company['city'] }} con financiación directa, cuotas a tu medida y acompañamiento hasta la escritura.
                    </p>
                </x-reveal>
                <x-reveal delay="300" class="mt-10 flex flex-col gap-3 sm:flex-row">
                    <a href="#estado-de-cuenta" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-orange-500 px-7 py-4 text-base font-semibold text-white shadow-xl shadow-orange-600/30 transition hover:bg-orange-400">
                        <x-icon name="document-text" class="h-5 w-5" />
                        Consulta tu estado de cuenta
                    </a>
                    <a href="#proyectos" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white/10 px-7 py-4 text-base font-semibold text-white ring-1 ring-inset ring-white/30 backdrop-blur transition hover:bg-white/20">
                        Conoce los proyectos
                        <x-icon name="arrow-right" class="h-5 w-5" />
                    </a>
                </x-reveal>
            </div>
        </div>
    </section>

    {{-- Figures and account statement --}}
    <section class="relative z-10 -mt-28 px-4 sm:px-6">
        <div class="mx-auto max-w-6xl">
            <x-reveal>
                <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-3xl bg-stone-200/70 shadow-2xl shadow-stone-900/10 ring-1 ring-stone-900/5 lg:grid-cols-4">
                    @foreach ($figures as $figure)
                        <div class="flex flex-col gap-1 bg-white px-5 py-6 sm:px-8 sm:py-7">
                            <dt class="order-2 text-sm text-stone-500">{{ $figure['label'] }}</dt>
                            <dd class="order-1 font-display text-3xl font-extrabold tracking-tight text-stone-900 sm:text-4xl">{{ $figure['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-reveal>

            <x-reveal id="estado-de-cuenta" class="mt-6 scroll-mt-28">
                <div class="grid overflow-hidden rounded-3xl bg-white shadow-xl shadow-stone-900/5 ring-1 ring-stone-900/5 lg:grid-cols-5">
                    <div class="relative isolate overflow-hidden bg-gradient-to-br from-orange-500 to-amber-500 p-8 text-white sm:p-10 lg:col-span-2">
                        <div class="absolute -right-16 -top-16 -z-10 h-56 w-56 rounded-full bg-white/10"></div>
                        <div class="absolute -bottom-20 -left-10 -z-10 h-48 w-48 rounded-full bg-white/10"></div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-orange-100">¿Ya eres cliente?</p>
                        <h2 class="mt-2 font-display text-3xl font-extrabold tracking-tight">Consulta tu estado de cuenta</h2>
                        <p class="mt-3 leading-relaxed text-orange-50">Revisa tus promesas y descarga tu estado de cuenta en PDF, sin registrarte.</p>
                        <ul role="list" class="mt-6 flex flex-col gap-2.5 text-sm font-medium">
                            @foreach (['Sin usuario ni contraseña', 'Descarga en PDF', 'Disponible las 24 horas'] as $point)
                                <li class="flex items-center gap-2">
                                    <x-icon name="check-circle" class="h-5 w-5 text-white" />
                                    {{ $point }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="flex flex-col justify-center p-6 sm:p-10 lg:col-span-3">
                        <p class="text-lg font-semibold text-stone-900">Escribe tu número de documento</p>
                        <p class="mt-1 text-sm text-stone-500">La cédula o el NIT con el que firmaste la promesa de compraventa.</p>
                        <div class="mt-5">
                            <livewire:public.buyer.statement.lookup />
                        </div>
                    </div>
                </div>
            </x-reveal>
        </div>
    </section>

    {{-- Projects --}}
    <section id="proyectos" class="scroll-mt-24 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <x-reveal class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-wider text-orange-600">Nuestros proyectos</p>
                    <h2 class="mt-2 font-display text-4xl font-extrabold tracking-tight text-stone-900 sm:text-5xl">Lotes para construir tu futuro</h2>
                    <p class="mt-4 text-lg leading-relaxed text-stone-600">Elige el proyecto que mejor se adapta a ti. La disponibilidad se actualiza con cada venta.</p>
                </div>
                <a href="{{ $whatsapp('Hola, quiero asesoría para elegir un lote.') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 self-start rounded-xl px-5 py-3 font-semibold text-stone-800 ring-1 ring-inset ring-stone-300 transition hover:bg-white lg:self-auto">
                    <x-icon name="chat-bubble-left-right" class="h-5 w-5 text-orange-500" />
                    Asesoría personalizada
                </a>
            </x-reveal>

            <div class="mt-14 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    @php($status = $statuses[$project['status']])
                    @php($soldPercent = $project['lots'] > 0 ? (int) round($project['sold'] / $project['lots'] * 100) : 0)
                    <x-reveal :delay="$loop->index * 120" class="h-full">
                        <article class="group flex h-full flex-col overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-stone-900/5 transition duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-stone-900/10">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="{{ asset($project['image']) }}" alt="Proyecto {{ $project['name'] }}" loading="lazy" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-stone-950/70 via-stone-950/0"></div>
                                <span class="absolute left-4 top-4 rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide shadow {{ $status['class'] }}">{{ $status['label'] }}</span>
                                <p class="absolute bottom-4 left-5 flex items-center gap-1.5 text-sm font-medium text-white">
                                    <x-icon name="map-pin" class="h-4 w-4 text-amber-300" />
                                    {{ $project['location'] }}
                                </p>
                            </div>

                            <div class="flex flex-1 flex-col p-6 sm:p-7">
                                <p class="text-xs font-semibold uppercase tracking-wider text-orange-600">{{ $project['tagline'] }}</p>
                                <h3 class="mt-1 font-display text-2xl font-bold tracking-tight text-stone-900">{{ $project['name'] }}</h3>
                                <p class="mt-3 leading-relaxed text-stone-600">{{ $project['description'] }}</p>

                                @if ($project['lots'] > 0)
                                    <dl class="mt-6 grid grid-cols-3 gap-2 rounded-2xl bg-stone-50 p-3 text-center ring-1 ring-inset ring-stone-100">
                                        <div>
                                            <dt class="text-xs text-stone-500">Lotes</dt>
                                            <dd class="font-display text-lg font-bold text-stone-900">{{ $number($project['lots']) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-stone-500">Disponibles</dt>
                                            <dd class="font-display text-lg font-bold text-emerald-600">{{ $number($project['available']) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-stone-500">Desde</dt>
                                            <dd class="font-display text-lg font-bold text-stone-900">{{ $project['min_area'] ? $project['min_area'].' m²' : '—' }}</dd>
                                        </div>
                                    </dl>
                                    <div class="mt-4">
                                        <div class="h-2 overflow-hidden rounded-full bg-stone-100" role="progressbar" aria-valuenow="{{ $soldPercent }}" aria-valuemin="0" aria-valuemax="100" aria-label="Lotes vendidos">
                                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-orange-500" style="width: {{ $soldPercent }}%"></div>
                                        </div>
                                        <p class="mt-1.5 text-xs text-stone-500">{{ $soldPercent }} % vendido</p>
                                    </div>
                                @elseif ($project['status'] === 'soon')
                                    <div class="mt-6 flex gap-3 rounded-2xl bg-amber-50 p-4 text-sm leading-relaxed text-amber-900 ring-1 ring-inset ring-amber-100">
                                        <x-icon name="sparkles" class="h-5 w-5 shrink-0 text-amber-500" />
                                        <p><span class="font-semibold">Preventa.</span> Sé de los primeros en elegir tu lote, con precio de lanzamiento.</p>
                                    </div>
                                @endif

                                @if ($project['features'])
                                    <ul role="list" class="mt-5 flex flex-wrap gap-2">
                                        @foreach ($project['features'] as $feature)
                                            <li class="rounded-full bg-orange-50 px-3 py-1 text-xs font-medium text-orange-700">{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                <div class="mt-auto pt-7">
                                    <a href="{{ $whatsapp($project['status'] === 'soon' ? "Hola, quiero que me avisen cuando abra el proyecto {$project['name']}." : "Hola, quiero información sobre los lotes del proyecto {$project['name']}.") }}" target="_blank" rel="noopener" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-stone-900 px-5 py-3 font-semibold text-white transition group-hover:bg-orange-500">
                                        {{ $project['status'] === 'soon' ? 'Avísame cuando abra' : 'Quiero información' }}
                                        <x-icon name="arrow-right" class="h-4 w-4" />
                                    </a>
                                </div>
                            </div>
                        </article>
                    </x-reveal>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why us --}}
    <section id="por-que-elegirnos" class="scroll-mt-24 overflow-hidden bg-white py-24 sm:py-32">
        <div class="mx-auto grid max-w-7xl items-center gap-16 px-6 lg:grid-cols-2 lg:gap-20 lg:px-8">
            <x-reveal class="relative">
                <div class="absolute -left-10 -top-10 -z-0 h-40 w-40 rounded-full bg-amber-100"></div>
                <img src="{{ asset('img/home/terrenos.webp') }}" alt="Vista aérea de terrenos" loading="lazy" class="relative aspect-square w-full rounded-[2rem] object-cover shadow-2xl shadow-stone-900/20">
                <div class="absolute -bottom-6 right-4 rounded-2xl bg-white p-5 shadow-xl ring-1 ring-stone-900/5 sm:-right-6 sm:p-6">
                    <p class="font-display text-4xl font-extrabold tracking-tight text-orange-500">{{ $number($stats['families']) }}</p>
                    <p class="mt-1 text-sm leading-snug text-stone-600">familias ya tienen<br>su lote con nosotros</p>
                </div>
                <div class="absolute left-4 top-4 flex items-center gap-2 rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-stone-800 shadow-lg backdrop-blur sm:left-6 sm:top-6">
                    <x-icon name="shield-check" class="h-5 w-5 text-emerald-600" />
                    {{ $number($stats['sold']) }} lotes vendidos
                </div>
            </x-reveal>

            <div>
                <x-reveal>
                    <p class="text-sm font-semibold uppercase tracking-wider text-orange-600">Por qué elegirnos</p>
                    <h2 class="mt-2 font-display text-4xl font-extrabold tracking-tight text-stone-900 sm:text-5xl">Invertir en tierra es invertir en tranquilidad</h2>
                    <p class="mt-4 text-lg leading-relaxed text-stone-600">Somos una empresa de {{ $company['city'] }} dedicada a que más familias del Casanare tengan un lugar propio, con condiciones claras desde el primer día.</p>
                </x-reveal>
                <dl class="mt-12 grid gap-8 sm:grid-cols-2">
                    @foreach ($benefits as $benefit)
                        <x-reveal :delay="$loop->index * 100">
                            <dt class="flex flex-col gap-4">
                                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-orange-500/10 text-orange-600">
                                    <x-icon :name="$benefit['icon']" class="h-6 w-6" />
                                </span>
                                <span class="text-lg font-semibold text-stone-900">{{ $benefit['title'] }}</span>
                            </dt>
                            <dd class="mt-2 leading-relaxed text-stone-600">{{ $benefit['text'] }}</dd>
                        </x-reveal>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- How to buy --}}
    <section id="como-comprar" class="relative isolate scroll-mt-24 overflow-hidden bg-stone-950 py-24 text-white sm:py-32">
        <div class="absolute -top-40 left-1/2 -z-10 h-[30rem] w-[60rem] -translate-x-1/2 rounded-full bg-orange-500/20 blur-3xl"></div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <x-reveal class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-400">Cómo comprar</p>
                <h2 class="mt-2 font-display text-4xl font-extrabold tracking-tight sm:text-5xl">Tu lote en cuatro pasos</h2>
                <p class="mt-4 text-lg leading-relaxed text-stone-400">Un proceso sencillo y transparente, con acompañamiento de principio a fin.</p>
            </x-reveal>
            <ol role="list" class="mt-16 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $step)
                    <li>
                        <x-reveal :delay="$loop->index * 120" class="h-full rounded-3xl bg-white/5 p-7 ring-1 ring-inset ring-white/10 hover:bg-white/10">
                            <span class="font-display text-5xl font-extrabold text-orange-500">{{ sprintf('%02d', $loop->iteration) }}</span>
                            <h3 class="mt-5 text-xl font-semibold">{{ $step['title'] }}</h3>
                            <p class="mt-2 leading-relaxed text-stone-400">{{ $step['text'] }}</p>
                        </x-reveal>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Location --}}
    <section id="ubicacion" class="scroll-mt-24 py-24 sm:py-32">
        <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-5 lg:px-8">
            <x-reveal class="flex flex-col lg:col-span-2">
                <p class="text-sm font-semibold uppercase tracking-wider text-orange-600">Ubicación</p>
                <h2 class="mt-2 font-display text-4xl font-extrabold tracking-tight text-stone-900 sm:text-5xl">Estamos en {{ $company['city'] }}, {{ $company['region'] }}</h2>
                <p class="mt-4 text-lg leading-relaxed text-stone-600">Visítanos en nuestra oficina o agenda una visita a los proyectos. Te mostramos los lotes disponibles sin compromiso.</p>

                <div class="mt-8 flex flex-col gap-5 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-stone-900/5">
                    <div class="flex gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-orange-500/10 text-orange-600"><x-icon name="map-pin" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold text-stone-900">Oficina de ventas</p>
                            <p class="text-stone-600">{{ $company['address'] }}, {{ $company['region'] }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-orange-500/10 text-orange-600"><x-icon name="clock" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold text-stone-900">Horario</p>
                            @foreach ($company['hours'] as $hours)
                                <p class="text-stone-600">{{ $hours }}</p>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-orange-500/10 text-orange-600"><x-icon name="phone" class="h-5 w-5" /></span>
                        <div>
                            <p class="font-semibold text-stone-900">Teléfono y WhatsApp</p>
                            <a href="tel:{{ $company['phone'] }}" class="text-stone-600 hover:text-orange-600">{{ $company['phone_display'] }}</a>
                        </div>
                    </div>
                </div>

                @if ($mapProjects->isNotEmpty())
                    <ul role="list" class="mt-6 flex flex-wrap gap-2">
                        @foreach ($mapProjects as $mapProject)
                            <li class="flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-sm font-medium text-stone-700 ring-1 ring-stone-200">
                                <span class="h-2.5 w-2.5 rounded-full bg-orange-500"></span>
                                {{ $mapProject['name'] }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-reveal>

            <x-reveal delay="150" class="lg:col-span-3">
                <div id="mapa" class="relative z-0 h-[24rem] w-full overflow-hidden rounded-3xl bg-stone-200 shadow-xl shadow-stone-900/10 ring-1 ring-stone-900/5 sm:h-[32rem]" role="region" aria-label="Mapa de los proyectos"></div>
            </x-reveal>
        </div>
    </section>

    {{-- Call to action --}}
    <section class="px-4 pb-24 sm:px-6">
        <x-reveal class="relative isolate mx-auto max-w-7xl overflow-hidden rounded-[2rem] bg-gradient-to-br from-orange-500 via-orange-500 to-amber-400 px-6 py-16 text-center shadow-2xl shadow-orange-500/20 sm:px-16 sm:py-20">
            <div class="absolute -left-20 -top-24 -z-10 h-72 w-72 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-32 -right-16 -z-10 h-80 w-80 rounded-full bg-white/10"></div>
            <h2 class="mx-auto max-w-2xl font-display text-4xl font-extrabold tracking-tight text-white sm:text-5xl">¿Listo para tener tu lote propio?</h2>
            <p class="mx-auto mt-4 max-w-xl text-lg text-orange-50">Escríbenos y un asesor te cuenta los lotes disponibles, precios y planes de pago.</p>
            <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ $whatsapp('Hola, quiero información sobre los lotes disponibles.') }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-7 py-4 font-semibold text-stone-900 shadow-lg transition hover:bg-orange-50">
                    <svg class="h-5 w-5 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47a8.9 8.9 0 0 1-1.65-2.05c-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.3 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.08 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35ZM12.05 21.8h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.72.97 1-3.62-.24-.37a9.8 9.8 0 1 1 8.33 4.6Zm8.34-18.13A11.73 11.73 0 0 0 12.05.2C5.55.2.27 5.48.27 11.97c0 2.08.54 4.1 1.58 5.89L.17 24l6.3-1.65a11.74 11.74 0 0 0 5.58 1.42h.01c6.49 0 11.77-5.28 11.78-11.78 0-3.14-1.22-6.1-3.45-8.32Z"/></svg>
                    Escríbenos por WhatsApp
                </a>
                <a href="tel:{{ $company['phone'] }}" class="inline-flex items-center justify-center gap-2 rounded-2xl px-7 py-4 font-semibold text-white ring-1 ring-inset ring-white/60 transition hover:bg-white/10">
                    <x-icon name="phone" class="h-5 w-5" />
                    {{ $company['phone_display'] }}
                </a>
            </div>
        </x-reveal>
    </section>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (() => {
            const element = document.getElementById('mapa');
            if (! element || typeof L === 'undefined') {
                return;
            }

            const projects = @json($mapProjects);
            const map = L.map(element, { scrollWheelZoom: false, zoomControl: true })
                .setView(@json(config('home.map_center')), 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map);

            const pin = L.divIcon({
                className: '',
                html: '<span style="display:grid;place-items:center;width:42px;height:42px;border-radius:9999px 9999px 9999px 0;transform:rotate(-45deg);background:linear-gradient(135deg,#fbbf24,#ea580c);box-shadow:0 10px 20px -5px rgba(234,88,12,.5);border:3px solid #fff"><span style="width:12px;height:12px;border-radius:9999px;background:#fff"></span></span>',
                iconSize: [42, 42],
                iconAnchor: [21, 42],
                popupAnchor: [0, -40],
            });

            const markers = projects.map((project) => {
                const popup = document.createElement('div');
                const name = document.createElement('strong');
                name.textContent = project.name;
                const status = document.createElement('div');
                status.textContent = project.status;
                popup.append(name, status);

                return L.marker([project.lat, project.lng], { icon: pin, title: project.name })
                    .bindPopup(popup)
                    .addTo(map);
            });

            if (markers.length > 1) {
                map.fitBounds(L.featureGroup(markers).getBounds().pad(0.4));
            }
        })();
    </script>
</x-public-layout>
