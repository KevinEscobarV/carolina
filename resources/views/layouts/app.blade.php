<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-cloak x-data="{ darkMode: $persist(true) }" :class="{ 'dark': darkMode === true }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Rivarca') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=Figtree:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite('resources/css/app.css')

        <!-- Styles -->
        @livewireStyles
    </head>

    <body class="font-sans antialiased">

        <x-wireui-notifications position="bottom-right" />
        <x-wireui-dialog blur="md" align="center" />
    
        <div class="flex h-screen bg-gray-100 dark:bg-gradient-to-tl dark:from-teal-950 dark:via-gray-800 dark:to-gray-800 transition">
    
            @livewire('components.sidebar-menu')
    
            <div class="flex-1 h-full overflow-x-hidden overflow-y-auto soft-scrollbar dark:bg-black/30">
    
                @livewire('navigation-menu')
    
                <x-banner />
    
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="container mx-auto py-10 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </header>
                @endif
    
                <!-- Page Content -->
                <main class="soft-scrollbar mb-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @vite('resources/js/app.js')
        @stack('modals')
        @livewireScripts
        @wireUiScripts
        @stack('scripts')
    </body>
</html>
