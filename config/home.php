<?php

/*
|--------------------------------------------------------------------------
| Homepage content
|--------------------------------------------------------------------------
|
| Texts, contact data and project details for the public homepage. The
| figures (lots, families, square meters) come from the database; this file
| only holds what the system does not know. Values marked "EJEMPLO" are
| placeholders until the company sends the real ones.
|
*/

return [

    // Shows a strip saying that some texts and pictures are examples
    'sample_content' => env('HOME_SAMPLE_CONTENT', true),

    'company' => [
        'name' => 'Loteos Carolina Riveros',
        'legal_name' => 'Martha Carolina Riveros Vargas',
        'nit' => '1.098.656.012-1',
        'city' => 'Yopal',
        'region' => 'Casanare',
        'phone' => '+573128854721',
        'phone_display' => '312 885 4721',
        'whatsapp' => '573128854721',
        'email' => 'carolinarv.ciudadelarivarca@gmail.com',
        'address' => 'Calle 10 # 20-30, Yopal', // EJEMPLO
        'hours' => [ // EJEMPLO
            'Lunes a viernes: 8:00 a. m. – 6:00 p. m.',
            'Sábados: 8:00 a. m. – 12:00 m.',
        ],
        'social' => [ // EJEMPLO
            'facebook' => 'https://www.facebook.com/',
            'instagram' => 'https://www.instagram.com/',
        ],
    ],

    // Map center (Yopal)
    'map_center' => [5.3378, -72.3959],

    /*
    | Keyed by the campaign name in the system. A campaign without an entry
    | here still shows up, with the default picture and texts.
    */
    'projects' => [
        'Rivarca' => [
            'image' => 'img/home/rivarca.webp',
            'tagline' => 'Ciudadela Rivarca', // EJEMPLO
            'location' => 'Sur de Yopal, Casanare', // EJEMPLO
            'description' => 'Nuestro primer proyecto: lotes residenciales con vías internas y espacio para construir la casa de tus sueños cerca del centro de Yopal.', // EJEMPLO
            'features' => ['Vías internas', 'Redes de servicios públicos', 'Escritura individual'], // EJEMPLO
            'coordinates' => [5.3100, -72.3946], // real: lots 13 and 395
        ],
        'Rivarca II' => [
            'image' => 'img/home/rivarca-ii.webp',
            'tagline' => 'Segunda etapa', // EJEMPLO
            'location' => 'Sur de Yopal, Casanare', // EJEMPLO
            'description' => 'La segunda etapa de Rivarca, con lotes más amplios y planes de pago flexibles. La mejor oportunidad para invertir hoy.', // EJEMPLO
            'features' => ['Lotes amplios', 'Zonas verdes', 'Financiación directa'], // EJEMPLO
            'coordinates' => [5.3055, -72.4010], // EJEMPLO
        ],
        'Villas del Teniente' => [
            'image' => 'img/home/villas-del-teniente.webp',
            'tagline' => 'Muy pronto', // EJEMPLO
            'location' => 'Yopal, Casanare', // EJEMPLO
            'description' => 'Un nuevo proyecto rodeado de naturaleza, pensado para vivir con tranquilidad. Déjanos tus datos y sé de los primeros en conocerlo.', // EJEMPLO
            'features' => ['Entorno natural', 'Lotes campestres', 'Preventa'], // EJEMPLO
            'coordinates' => [5.3555, -72.4105], // EJEMPLO
        ],
    ],

    'default_project' => [
        'image' => 'img/home/rivarca.webp',
        'tagline' => 'Proyecto',
        'location' => 'Yopal, Casanare',
        'description' => 'Lotes con financiación directa y acompañamiento hasta la escritura.',
        'features' => [],
        'coordinates' => null,
    ],

];
