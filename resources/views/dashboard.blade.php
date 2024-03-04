<x-app-layout>
    <section class="text-gray-600 body-font relative h-screen">
        <div class="absolute inset-0 bg-gray-300">
            <iframe width="100%" height="100%" frameborder="0" marginheight="0" marginwidth="0" title="map"
                scrolling="no"
                src="https://maps.google.com/maps?width=100%25&amp;height=100%&amp;hl=en&amp;q=yopal%20casanare+(ur%20Business%20Name)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
                style="filter: grayscale(1.0) contrast(1.2) opacity(0.7);"></iframe>
        </div>
        <div class="container px-5 pt-24 pb-48 mx-auto flex h-full">
            <div class="lg:w-2/3 rounded-2xl opacity-80 p-8 flex flex-col md:ml-auto w-full mt-10 md:mt-0 relative z-10 shadow-md bg-cover bg-center animate-pulse" 
            style="background-image: url({{asset('img/home.jpg')}})">
            </div>
        </div>
    </section>
</x-app-layout>
