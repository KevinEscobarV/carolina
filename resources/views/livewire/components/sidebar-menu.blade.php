<aside class="flex-shrink-0 hidden z-20 bg-gray-50 dark:bg-gray-900/95 border-r border-primary-800/50 dark:border-gray-700 md:block" x-data="{ open: '{{ Auth::user()->expanded_sidebar }}' }">
    <div class="flex flex-col h-full transition-all duration-500" :class="{ 'w-52': open, 'w-20': !open }">

        <div class="flex items-center justify-center px-4 my-6">
            <x-application-mark class="text-gray-500 dark:text-gray-200 w-full" />
        </div>

        <div class="flex flex-col items-center justify-center w-full my-4">
            <div class="self-end mt-2 -mb-3">
                <div x-on:click="open = ! open; $wire.toggleSidebar()" class="px-2 py-1 flex justify-center items-center bg-white dark:bg-gray-800 rounded-full border border-primary-800 dark:border-gray-700 shadow-md cursor-pointer -mr-4">
                    <button class="text-primary-800 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5 transition-transform transform duration-1000" :class="{ 'rotate-180': open }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar links -->
        <nav aria-label="Main" class="flex flex-col h-full py-3 mt-4 soft-scrollbar overflow-hidden hover:overflow-y-auto">
            <x-side-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="chart-pie">
                <span x-show="open" x-transition.duration.500ms>Administración</span>
            </x-side-link>
            
            {{-- <x-side-link href="#" icon="chart-pie">
                <span x-show="open" x-transition.duration.500ms>Reportes</span>
            </x-side-link> --}}

            @can('view.buyers')
                <x-side-link href="{{ route('buyers') }}" :active="request()->routeIs(['buyers', 'buyers.*'])" icon="users">
                    <span x-show="open" x-transition.duration.500ms>Clientes</span>
                </x-side-link>
            @endcan

            @can('view.payments')
                <x-side-link href="{{ route('payments') }}" :active="request()->routeIs(['payments', 'payments.*'])" icon="arrow-trending-up">
                    <span x-show="open" x-transition.duration.500ms>Pagos</span>
                </x-side-link>
            @endcan

            @can('view.promises')
                <x-side-link href="{{ route('promises') }}" :active="request()->routeIs(['promises', 'promises.*'])" icon="hand-raised">
                    <span x-show="open" x-transition.duration.500ms>Promesas</span>
                </x-side-link>
            @endcan

            @can('view.blocks')
                <x-side-link href="{{ route('blocks') }}" :active="request()->routeIs(['blocks', 'blocks.*'])" icon="squares-plus">
                    <span x-show="open" x-transition.duration.500ms>Manzanas</span>
                </x-side-link>
            @endcan

            @can('view.parcels')
                <x-side-link href="{{ route('parcels') }}" :active="request()->routeIs(['parcels', 'parcels.*'])" icon="map">
                    <span x-show="open" x-transition.duration.500ms>Lotes</span>
                </x-side-link>
            @endcan

            @can('view.deeds')
                <x-side-link href="{{ route('deeds') }}" :active="request()->routeIs(['deeds', 'deeds.*'])" icon="scale">
                    <span x-show="open" x-transition.duration.500ms>Escrituras</span>
                </x-side-link>
            @endcan

            @can('view.contegories')
                <x-side-link href="{{ route('categories') }}" :active="request()->routeIs(['categories', 'categories.*'])" icon="megaphone">
                    <span x-show="open" x-transition.duration.500ms>Campañas</span>
                </x-side-link>
            @endcan

            @can('send.messages')
                <x-side-link href="{{ route('notifications') }}" :active="request()->routeIs(['notifications', 'notifications.*'])" icon="bell">
                    <span x-show="open" x-transition.duration.500ms>Notificaciones</span>
                </x-side-link>
            @endcan

            @can('edit.settings')
                <x-side-link href="{{ route('settings') }}" :active="request()->routeIs(['settings', 'settings.*'])" icon="cog-6-tooth">
                    <span x-show="open" x-transition.duration.500ms>Configuración</span>
                </x-side-link>
            @endcan
                
            @can('view.users')
                <x-side-link href="{{ route('users') }}" :active="request()->routeIs(['users', 'settings.*'])" icon="user-group">
                    <span x-show="open" x-transition.duration.500ms>Usuarios</span>
                </x-side-link>
            @endcan

            @can('view.roles')
                <x-side-link href="{{ route('permissions') }}" :active="request()->routeIs(['permissions', 'permissions.*'])" icon="key">
                    <span x-show="open" x-transition.duration.500ms>Roles</span>
                </x-side-link>
            @endcan
        </nav>

        <!-- Sidebar footer -->
        <div class="flex-shrink-0 border-t border-gray-100 dark:border-gray-700 px-2 py-4 space-y-2">
            <div class="p-4">
                <div class="inline-flex items-center gap-4 leading-4 font-light text-primary-900 rounded mr-4 dark:text-gray-200">
                    <span x-show="open" x-transition.duration.500ms>LOTEOS M.C.R.V</span>
                </div>
            </div>
        </div>
    </div>
</aside>