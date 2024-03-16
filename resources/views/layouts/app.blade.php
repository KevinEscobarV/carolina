<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-cloak x-data="{ darkMode: $persist(false) }" :class="{ 'dark': darkMode === true }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? config('app.name', 'Rivarca') . ' | ' . $title : config('app.name', 'Rivarca') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Figtree:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css'])

    <!-- Styles -->
    @livewireStyles

    <style>
        .soft-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
            cursor: pointer;
        }

        .soft-scrollbar::-webkit-scrollbar-track {
            background-color: #e2e8f0;
            cursor: pointer;
        }

        .soft-scrollbar::-webkit-scrollbar-thumb {
            cursor: pointer;
            background-color: #ffae35;
            border-radius: 2px;
        }

        .dark .soft-scrollbar::-webkit-scrollbar-track {
            background-color: #475569;
            cursor: pointer;
        }

        .dark .soft-scrollbar::-webkit-scrollbar-thumb {
            cursor: pointer;
            background-color: #df8d13;
            border-radius: 2px;
        }
    </style>
</head>

<body class="font-sans antialiased">

    <x-wireui-notifications position="bottom-right" />
    <x-wireui-dialog align="center" />

    <div class="flex h-screen bg-cover bg-center" style="background-image: url('{{ asset('img/background2.jpg') }}')">

        @livewire('components.sidebar-menu')

        <div class="flex-1 h-full overflow-x-hidden overflow-y-auto soft-scrollbar bg-gray-50/80 dark:bg-gray-900/95 transition">

            @livewire('navigation-menu')

            <x-banner />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </header>
            @endif

            <!-- Page Content -->
            <main class="soft-scrollbar pb-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script defer src="https://unpkg.com/@alpinejs/ui@3.13.3-beta.4/dist/cdn.min.js"></script>
    @vite('resources/js/app.js')
    @livewireScripts
    @wireUiScripts
    @stack('modals')
    @stack('scripts')
    @if (app()->isProduction())
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.hook('request', ({ fail }) => {
                    fail(({ status, preventDefault }) => {
                        if (status === 419) {
                            window.$wireui.notify({
                                title: 'Error!',
                                description: 'La página ha expirado debido a la inactividad, esta se recargará automáticamente.',
                                icon: 'error'
                            })

                            // Esperar 2 segundos antes de recargar la página
                            setTimeout(() => {
                                window.location.reload()
                            }, 2000)

                            preventDefault()
                        }
                        if (status >= 500) {
                            window.$wireui.notify({
                                title: 'Error!',
                                description: 'Algo salió mal. Por favor, inténtelo de nuevo.',
                                icon: 'error'
                            })

                            preventDefault()
                        }
                    })
                })
            })
        </script>
    @endif
</body>
</html>
