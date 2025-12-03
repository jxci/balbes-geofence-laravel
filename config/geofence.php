<?php

return [
    'default_service' => 'dadata',

    'fallback' => [
        'enabled' => true,
        'service' => 'yandex_geo',
    ],

    'dadata' => [
        'base_uri' => env('DADATA_BASE_URI', 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/'),
        'timeout' => env('DADATA_TIMEOUT', 3.0),
        'api_key' => env('DADATA_API_KEY'),
        'secret' => env('DADATA_SECRET'),

        'logging' => [
            'log_requests' => true,
            'log_responses' => true,
            'log_errors' => true,
        ],
    ],

    'yandex_geo' => [
        'base_uri' => env('YANDEX_GEO_BASE_URI', 'https://geocode-maps.yandex.ru/1.x/'),
        'timeout' => env('YANDEX_GEO_TIMEOUT', 3.0),
        'api_key' => env('YANDEX_GEO_API_KEY'),

        'logging' => [
            'log_requests' => true,
            'log_responses' => true,
            'log_errors' => true,
        ],
    ],
];


