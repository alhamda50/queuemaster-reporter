<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable / Disable Reporting
    |--------------------------------------------------------------------------
    */
    'enabled' => env('QUEUEMASTER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | QueueMaster API Configuration
    |--------------------------------------------------------------------------
    */
    'server_url' => env('QUEUEMASTER_SERVER_URL', 'http://localhost:8000'),
    'api_token' => env('QUEUEMASTER_API_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Async Queue Configuration
    |--------------------------------------------------------------------------
    | The connection and queue to use for the report job itself (to not block)
    */
    'queue_connection' => env('QUEUEMASTER_QUEUE_CONNECTION', 'database'),
    'queue' => env('QUEUEMASTER_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Ignored Jobs
    |--------------------------------------------------------------------------
    | Fully qualified class names of jobs to ignore.
    */
    'except' => [
        // App\Jobs\SomeNoisyJob::class,
    ],
];
