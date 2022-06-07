<?php

return [
    'host' => env('WHATSAPP_HOST', 'http://127.0.0.1'),
    'port' => env('WHATSAPP_PORT', 8000),
    'uri' => env('WHATSAPP_URI', '/'),

    'session_id' => env('WHATSAPP_SESSION', 'wa-notif'),
    'max_retries' => 3,
    'reconnect_interval' => 5, //in seconds
    'delay' => 5 //in seconds
];
