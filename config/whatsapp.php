<?php

return [
    'url' => env('WHATSAPP_URL', 'http://127.0.0.1'),
    'uri' => env('WHATSAPP_URI', '/'),

    'session_id' => env('WHATSAPP_SESSION', 'wa-notif'),
    'token' => env('WHATSAPP_TOKEN', 'wa-token'),

    'max_retries' => 3,
    'reconnect_interval' => 5, //in seconds
    'delay' => 5, //in seconds

    'queue' => [
        'enabled' => (bool) env('WHATSAPP_QUEUE_ENABLED', false),
        'name' => 'default',
        'connection' => env('QUEUE_CONNECTION', 'redis'),
    ],

    /**
     * Country code
     * @see http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements
     */
    'country' => [
        'default' => 'ID'
    ]
];
