<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin password
    |--------------------------------------------------------------------------
    |
    | Used only by CvSeeder when creating the single admin user. Kept out of
    | the codebase deliberately -- set ADMIN_PASSWORD in .env before seeding.
    |
    */
    'admin_password' => env('ADMIN_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Supported locales
    |--------------------------------------------------------------------------
    |
    | Drives the public URL prefix (/es, /en) and the ES|EN tabs in the admin.
    |
    */
    'locales' => ['es', 'en'],
];
