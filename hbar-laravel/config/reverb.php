<?php

return [
    'app_id' => env('REVERB_APP_ID'),
    'app_key' => env('REVERB_APP_KEY'),
    'app_secret' => env('REVERB_APP_SECRET'),
    'host' => env('REVERB_HOST', '0.0.0.0'),
    'port' => env('REVERB_PORT', 8080),
    'hostname' => env('REVERB_HOSTNAME'),
    'cluster' => env('REVERB_CLUSTER', 'us2'),
    'database' => 'reverb',
    'driver' => env('REVERB_DRIVER', 'laravel'),
    'max_request_size' => 10,
    'ping_interval' => 30,
    'max_connections_per_app' => null,
    'max_message_size' => 10_000,
    'allowed_origins' => ['*'],
    'ssl' => [
        'enabled' => env('REVERB_SSL_ENABLED', false),
        'certificate' => env('REVERB_SSL_CERTIFICATE'),
        'key' => env('REVERB_SSL_KEY'),
    ],
];
