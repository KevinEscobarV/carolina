@php
    $company = config('home.company');
    $pageTitle = isset($title) && $title ? "{$title} | {$company['name']}" : "{$company['name']} | Lotes en Yopal, Casanare";
    $pageDescription = $description ?? 'Lotes en Yopal, Casanare, con financiación directa y acompañamiento hasta la escritura. Conoce nuestros proyectos y consulta tu estado de cuenta en línea.';
    $onHome = request()->routeIs('home');
    $statementHref = $onHome ? '#estado-de-cuenta' : route('personal-account-statement');
    $whatsappHref = 'https://wa.me/'.$company['whatsapp'].'?text='.rawurlencode('Hola, quiero información sobre los lotes.');
    $navigation = [
        ['label' => 'Proyectos', 'href' => route('home').'#proyectos'],
        ['label' => 'Por qué elegirnos', 'href' => route('home').'#por-que-elegirnos'],
        ['label' => 'Cómo comprar', 'href' => route('home').'#como-comprar'],
        ['label' => 'Ubicación', 'href' => route('home').'#ubicacion'],
    ];
@endphp
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDescription }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="theme-color" content="#f97316">
        <x-favicon />

        <meta property="og:type" content="website">
        <meta property="og:locale" content="es_CO">
        <meta property="og:site_name" content="{{ $company['name'] }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('img/home/og.jpg') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta name="twitter:card" content="summary_large_image">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|plus-jakarta-sans:600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @stack('head')
    </head>
    <body class="bg-stone-50 font-sans text-stone-700 antialiased selection:bg-orange-200 selection:text-stone-900">
        <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:text-stone-900">Saltar al contenido</a>

        {{-- Header --}}
        <header x-data="{ open: false }" @keydown.escape.window="open = false" class="fixed inset-x-0 top-0 z-50 px-3 pt-3 sm:px-6">
            <nav aria-label="Principal" class="mx-auto flex max-w-7xl items-center justify-between gap-2 rounded-2xl bg-white/85 px-4 py-2.5 shadow-lg shadow-stone-900/5 ring-1 ring-stone-900/5 backdrop-blur-md sm:px-5">
                <a href="{{ route('home') }}" aria-label="{{ $company['name'] }}, inicio">
                    <x-brand-logo />
                </a>

                <div class="hidden items-center gap-1 lg:flex">
                    @foreach ($navigation as $item)
                        <a href="{{ $item['href'] }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-100 hover:text-stone-900">{{ $item['label'] }}</a>
                    @endforeach
                </div>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="hidden items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-stone-700 ring-1 ring-inset ring-stone-300 transition hover:bg-stone-100 sm:inline-flex">
                            <x-icon name="squares-2x2" class="h-4 w-4" />
                            Ir al panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-stone-700 ring-1 ring-inset ring-stone-300 transition hover:bg-stone-100 sm:inline-flex">
                            <x-icon name="lock-closed" class="h-4 w-4" />
                            Ingreso administrativo
                        </a>
                    @endauth
                    <a href="{{ $statementHref }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-orange-500 px-3 py-2.5 text-sm font-semibold text-white shadow-md shadow-orange-500/30 transition hover:bg-orange-600 sm:px-4">
                        <x-icon name="document-text" class="h-4 w-4" />
                        <span class="hidden sm:inline">Estado de cuenta</span>
                        <span class="sm:hidden">Mi cuenta</span>
                    </a>
                    <button type="button" @click="open = ! open" :aria-expanded="open" aria-controls="menu-movil" class="grid h-10 w-10 place-items-center rounded-xl text-stone-700 ring-1 ring-inset ring-stone-300 lg:hidden">
                        <span class="sr-only">Abrir menú</span>
                        <x-icon name="bars-3" class="h-5 w-5" x-show="! open" />
                        <x-icon name="x-mark" class="h-5 w-5" x-show="open" x-cloak />
                    </button>
                </div>
            </nav>

            <div id="menu-movil" x-show="open" x-cloak x-transition.origin.top @click.outside="open = false" class="mx-auto mt-2 max-w-7xl rounded-2xl bg-white p-3 shadow-xl ring-1 ring-stone-900/5 lg:hidden">
                <div class="flex flex-col gap-1">
                    @foreach ($navigation as $item)
                        <a href="{{ $item['href'] }}" @click="open = false" class="rounded-xl px-4 py-3 font-medium text-stone-700 hover:bg-stone-100">{{ $item['label'] }}</a>
                    @endforeach
                </div>
                <div class="mt-3 grid gap-2 border-t border-stone-100 pt-3 sm:grid-cols-2">
                    <a href="{{ $statementHref }}" @click="open = false" class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-3 font-semibold text-white">
                        <x-icon name="document-text" class="h-5 w-5" />
                        Consultar estado de cuenta
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 font-semibold text-stone-700 ring-1 ring-inset ring-stone-300">
                            <x-icon name="squares-2x2" class="h-5 w-5" />
                            Ir al panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 font-semibold text-stone-700 ring-1 ring-inset ring-stone-300">
                            <x-icon name="lock-closed" class="h-5 w-5" />
                            Ingreso administrativo
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <x-banner />

        <main id="contenido">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer id="contacto" class="scroll-mt-24 bg-stone-950 text-stone-400">
            <div class="mx-auto grid max-w-7xl gap-12 px-6 py-16 lg:grid-cols-12 lg:px-8">
                <div class="lg:col-span-4">
                    <x-brand-logo dark />
                    <p class="mt-5 max-w-sm leading-relaxed">Hacemos realidad el sueño de tener un lote propio en Yopal, con financiación directa y acompañamiento en cada paso.</p>
                    <div class="mt-6 flex gap-3">
                        <a href="{{ $company['social']['facebook'] }}" target="_blank" rel="noopener" class="grid h-10 w-10 place-items-center rounded-xl bg-white/5 text-stone-300 transition hover:bg-orange-500 hover:text-white" aria-label="Facebook">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12Z"/></svg>
                        </a>
                        <a href="{{ $company['social']['instagram'] }}" target="_blank" rel="noopener" class="grid h-10 w-10 place-items-center rounded-xl bg-white/5 text-stone-300 transition hover:bg-orange-500 hover:text-white" aria-label="Instagram">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.72 3.72 0 0 1-1.38-.9 3.72 3.72 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16ZM12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63a5.88 5.88 0 0 0-2.13 1.38A5.88 5.88 0 0 0 .63 4.14C.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.79.72 1.46 1.38 2.13a5.88 5.88 0 0 0 2.13 1.38c.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.88 5.88 0 0 0 2.13-1.38 5.88 5.88 0 0 0 1.38-2.13c.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91a5.88 5.88 0 0 0-1.38-2.13A5.88 5.88 0 0 0 19.86.63C19.1.33 18.22.13 16.95.07 15.67.01 15.26 0 12 0Zm0 5.84a6.16 6.16 0 1 0 0 12.32 6.16 6.16 0 0 0 0-12.32ZM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm6.4-11.85a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88Z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="grid gap-10 sm:grid-cols-3 lg:col-span-8">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-white">Enlaces</h2>
                        <ul role="list" class="mt-4 flex flex-col gap-3">
                            @foreach ($navigation as $item)
                                <li><a href="{{ $item['href'] }}" class="transition hover:text-orange-400">{{ $item['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-white">Clientes</h2>
                        <ul role="list" class="mt-4 flex flex-col gap-3">
                            <li><a href="{{ $statementHref }}" class="transition hover:text-orange-400">Consultar estado de cuenta</a></li>
                            <li><a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="transition hover:text-orange-400">Escríbenos por WhatsApp</a></li>
                            <li>
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 transition hover:text-orange-400">
                                    <x-icon name="lock-closed" class="h-4 w-4" />
                                    Ingreso administrativo
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-white">Contacto</h2>
                        <ul role="list" class="mt-4 flex flex-col gap-3">
                            <li class="flex gap-3">
                                <x-icon name="phone" class="mt-0.5 h-5 w-5 shrink-0 text-orange-400" />
                                <a href="tel:{{ $company['phone'] }}" class="transition hover:text-orange-400">{{ $company['phone_display'] }}</a>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="envelope" class="mt-0.5 h-5 w-5 shrink-0 text-orange-400" />
                                <a href="mailto:{{ $company['email'] }}" class="break-all transition hover:text-orange-400">{{ $company['email'] }}</a>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="map-pin" class="mt-0.5 h-5 w-5 shrink-0 text-orange-400" />
                                <span>{{ $company['address'] }}, {{ $company['region'] }}</span>
                            </li>
                            <li class="flex gap-3">
                                <x-icon name="clock" class="mt-0.5 h-5 w-5 shrink-0 text-orange-400" />
                                <span>{!! implode('<br>', array_map('e', $company['hours'])) !!}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="border-t border-white/10">
                <div class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-6 text-sm sm:flex-row sm:items-center sm:justify-between lg:px-8">
                    <p>© {{ now()->year }} {{ $company['legal_name'] }} · NIT {{ $company['nit'] }}</p>
                    <p>{{ $company['city'] }}, {{ $company['region'] }} · Colombia</p>
                </div>
            </div>
        </footer>

        {{-- WhatsApp --}}
        <a href="{{ $whatsappHref }}" target="_blank" rel="noopener" class="group fixed bottom-5 right-5 z-40 flex items-center gap-3" aria-label="Escríbenos por WhatsApp">
            <span class="hidden rounded-full bg-white px-4 py-2 text-sm font-semibold text-stone-800 shadow-lg ring-1 ring-stone-900/5 transition group-hover:translate-x-0 sm:block">¿Hablamos?</span>
            <span class="grid h-14 w-14 place-items-center rounded-full bg-[#25D366] text-white shadow-xl shadow-green-600/30 transition group-hover:scale-105">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47a8.9 8.9 0 0 1-1.65-2.05c-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.3 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.08 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35ZM12.05 21.8h-.01a9.8 9.8 0 0 1-5-1.37l-.36-.21-3.72.97 1-3.62-.24-.37a9.8 9.8 0 1 1 8.33 4.6Zm8.34-18.13A11.73 11.73 0 0 0 12.05.2C5.55.2.27 5.48.27 11.97c0 2.08.54 4.1 1.58 5.89L.17 24l6.3-1.65a11.74 11.74 0 0 0 5.58 1.42h.01c6.49 0 11.77-5.28 11.78-11.78 0-3.14-1.22-6.1-3.45-8.32Z"/></svg>
            </span>
        </a>

        @if (config('home.sample_content'))
            <div class="fixed bottom-5 left-3 z-40 max-w-[11rem] rounded-2xl bg-stone-900/90 px-3 py-2 text-[11px] leading-snug text-stone-200 shadow-xl backdrop-blur sm:left-5 sm:max-w-[15rem] sm:px-4 sm:py-3 sm:text-xs">
                <span class="font-semibold text-amber-300">Vista previa.</span> Algunas fotos y textos son de ejemplo.
            </div>
        @endif

        @livewireScripts
    </body>
</html>
