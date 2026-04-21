<?php

namespace QueueMaster\Reporter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use QueueMaster\Reporter\QueueMasterReporter;

class ReportJobStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(public array $payload) {}

    public function handle(QueueMasterReporter $reporter): void
    {
        \Illuminate\Support\Facades\Log::info('QueueMaster: Processing reporting job for ' . ($this->payload['name'] ?? 'unknown'));
        $reporter->send($this->payload);
    }
}
