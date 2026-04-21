<?php

namespace QueueMaster\Reporter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobFailed;
use QueueMaster\Reporter\Jobs\ReportJobStatusJob;

class QueueMasterReporterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/queuemaster.php' => config_path('queuemaster.php'),
        ], 'queuemaster-config');

        if (!config('queuemaster.enabled')) {
            return;
        }

        Queue::before(function (JobProcessing $event) {
            $this->report('pending', $event);
            $this->report('running', $event); // Simplification due to no robust started-only hook
        });

        Queue::after(function (JobProcessed $event) {
            $this->report('succeeded', $event);
        });

        Queue::failing(function (JobFailed $event) {
            $this->report('failed', $event, $event->exception);
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/queuemaster.php', 'queuemaster'
        );

        // Register the retry webhook
        \Illuminate\Support\Facades\Route::post('/queuemaster/webhook/retry', \QueueMaster\Reporter\Http\Controllers\RetryWebhookController::class)
            ->middleware('api');
    }

    protected function report($status, $event, $exception = null)
    {
        $jobName = $event->job->resolveName();
        $except = config('queuemaster.except', []);
        
        // Prevent infinite loop if using queue for reporting
        if ($jobName === ReportJobStatusJob::class || in_array($jobName, $except)) {
            return;
        }

        $payloadBase = [
            'uuid' => $event->job->uuid(),
            'name' => $jobName,
            'status' => $status,
            'connection' => $event->connectionName,
            'queue' => $event->job->getQueue(),
            'attempt' => $event->job->attempts(),
        ];

        if ($status === 'pending') {
            $payloadBase['payload'] = json_decode($event->job->getRawBody(), true);
        }

        if ($status === 'running') {
            $payloadBase['started_at'] = now()->toDateTimeString();
        }

        if (in_array($status, ['succeeded', 'failed'])) {
            $payloadBase['finished_at'] = now()->toDateTimeString();
        }

        if ($exception) {
            $payloadBase['exception'] = $exception->getMessage() . "\n" . $exception->getTraceAsString();
        }

        // Dispatch async job so we don't slow down the main queue workers
        ReportJobStatusJob::dispatch($payloadBase)
            ->onConnection(config('queuemaster.queue_connection'))
            ->onQueue(config('queuemaster.queue'));
    }
}
