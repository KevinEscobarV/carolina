<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Crear Lote
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8">
    <div class="grid md:grid-cols-2 grid-cols-1 gap-8">
        <x-card>
            <form wire:submit.prevent="save">
                <x-parcel.form />
                <div class="flex items-center justify-end gap-2 mt-6">
                    <x-wireui-button type="submit" spinner="save" lime label="Crear Lote" />
                </div>
            </form>
        </x-card>
        <x-card>
            <div x-data="map" wire:ignore x-init="initMap()" class="rounded-lg overflow-hidden dark:bg-gray-50">
                <div id="map" class="h-[550px] w-full"></div>
            </div>
        </x-card>
    </div>
</div>

@assets
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />

    <style>
        :is(.dark){
            .leaflet-layer,
            .leaflet-control-zoom-in,
            .leaflet-control-zoom-out,
            .leaflet-control-attribution {
                filter: invert(100%) hue-rotate(180deg) brightness(95%) contrast(90%);
            }
        }
    </style>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.js"></script>
@endassets

@script
    <script>
        Alpine.data('map', () => ({
            initMap() {

                let map = L.map('map').setView([5.3174118648923745, -72.40578830189148], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                }).addTo(map);


                // add Leaflet-Geoman controls with some options to the map  
                map.pm.addControls({
                    position: 'topright',
                    drawCircleMarker: false,
                    rotateMode: false,
                });

                map.on("pm:create", (e) => {
                    this.$wire.setCoordinates(e.shape, e.shape == 'Marker' ? e.layer._latlng : e.layer._latlngs);
                });
            }
        }))
    </script>
@endscript
