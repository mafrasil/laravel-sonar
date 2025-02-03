<?php

// config for Mafrasil/LaravelSonar
return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard Configuration
    |--------------------------------------------------------------------------
    |
    | Enable or disable the Sonar dashboard and configure its settings.
    |
     */
    'dashboard' => [
        'enabled' => env('SONAR_DASHBOARD_ENABLED', config('app.debug')),
        'path' => 'sonar', // URI path for the Sonar dashboard
    ],

    'allowed_emails' => [
        // 'admin@example.com'
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the route prefix and middleware for Sonar endpoints.
    |
     */
    'route' => [
        'prefix' => 'sonar',
        'middleware' => ['web', 'auth'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the client-side event queue behavior
    |
     */
    'queue' => [
        'batch_size' => 10, // Maximum number of events to send in one request
        'flush_interval' => 1000, // Milliseconds to wait before sending queued events
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Types
    |--------------------------------------------------------------------------
    |
    | Define the allowed event types. Add custom types as needed.
    |
     */
    'event_types' => [
        'click',
        'hover',
        'impression',
        'custom',
    ],
];
