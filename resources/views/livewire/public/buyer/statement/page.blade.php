<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-black/30">
    <div class="w-full sm:max-w-md mt-6 p-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
        <div class="flex justify-center mb-8 mt-4">
            <x-authentication-card-logo />
        </div>
        <form wire:submit.prevent="explore">
            <div class="grid grid-cols-6 gap-6 mt-20">
                <div class="col-span-6">
                    <div class="flex items-center gap-3">
                        <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                            <x-icon name="scale" class="h-7 text-primary-500" />
                        </div>
                        <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                            Ingresa tu número de documento
                        </h2>
                    </div>
                </div>
                <div class="col-span-6">
                    <x-wireui-input label="Numero de documento" right-icon="finger-print" wire:model="document_number" />
                </div>
            </div>
            <div class="flex items-center gap-2 mt-6">
                <x-wireui-button class="w-full" icon="search" type="submit" spinner="explore" lg primary label="Buscar" />
            </div>
        </form>
        @if ($promises)
            <div class="mt-6">
                <div class="flex items-center gap-3">
                    <div class="border-2 border-primary-500 rounded-full h-10 w-10 flex items-center justify-center">
                        <x-icon name="hand-raised" class="h-7 text-primary-500" />
                    </div>
                    <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Promesas encontradas
                    </h2>
                </div>
            </div>
            <div class="mt-6 -m-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Fecha
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Valor
                                </th>
                                <th>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                            @foreach ($promises as $promise)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-200 first-letter:uppercase">{{ $promise->signature_date ? $promise->signature_date->translatedFormat("d/m/Y") : 'Sin definir' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-gray-400">$</span> {{ $promise->value_formatted }} <span class="text-gray-400 text-sm">COP</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-wireui-button icon="eye" label="Ver" primary target="_blank" href="{{ route('statement-download', [$promise->id, $buyer->id]) }}" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
