<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Proxies whose X-Forwarded-* headers are believed: "*" for a load balancer
    | in front of the app (DigitalOcean App Platform), or a comma-separated list
    | of addresses. Leave it empty when the app is served directly (cPanel), so
    | a client cannot fake its IP address or the https scheme with a header.
    |
    */

    'proxies' => env('TRUSTED_PROXIES'),

];
