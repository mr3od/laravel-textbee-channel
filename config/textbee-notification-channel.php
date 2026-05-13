<?php

return [
    'enabled'   => env('TEXTBEE_ENABLED', true),

    'api_key'   => env('TEXTBEE_API_KEY'),

    'device_id' => env('TEXTBEE_DEVICE_ID'),

    'base_url'  => env('TEXTBEE_BASE_URL', 'https://api.textbee.dev/api/v1'),

    // Used for local development – all messages are sent to this number
    'debug_to'  => env('TEXTBEE_DEBUG_TO'),
];
