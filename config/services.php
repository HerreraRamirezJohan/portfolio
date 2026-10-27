<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    | ExchangeRate-API, open-access endpoint (no key). Rates refresh once a day
    | and the provider asks for at most hourly requests, so a result is kept
    | `ttl` seconds; after a failed call the old rate is reused and the call is
    | not repeated for `retry_ttl` seconds. `fallback` is the number of units
    | per US dollar used when nothing has ever been fetched. Pages that show a
    | converted rate must credit "Rates By Exchange Rate API" (see Home.jsx).
    */
    'exchange_rate' => [
        'url' => env('EXCHANGE_RATE_URL', 'https://open.er-api.com/v6/latest/USD'),
        'ttl' => (int) env('EXCHANGE_RATE_TTL', 21600),
        'retry_ttl' => (int) env('EXCHANGE_RATE_RETRY_TTL', 300),
        'fallback' => [
            'MXN' => (float) env('EXCHANGE_RATE_FALLBACK_MXN', 18.3),
        ],
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
