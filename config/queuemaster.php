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
    'api_endpoint' => env('QUEUEMASTER_ENDPOINT', 'http://localhost:8000/api/v1/jobs/events'),
    'api_token' => env('QUEUEMASTER_TOKEN', ''),

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
