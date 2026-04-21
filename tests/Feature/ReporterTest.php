<?php

namespace QueueMaster\Reporter\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use QueueMaster\Reporter\Tests\TestCase;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;

class ReporterTest extends TestCase
{
    public function test_it_sends_payload_to_api_on_job_processing()
    {
        Http::fake([
            'queuemaster.test/*' => Http::response(['message' => 'Accepted'], 202),
        ]);

        config(['queuemaster.api_endpoint' => 'http://queuemaster.test/api/v1/jobs/events']);
        config(['queuemaster.api_token' => 'test-token']);
        config(['queuemaster.enabled' => true]);

        // Mock a job
        $job = \Mockery::mock(\Illuminate\Contracts\Queue\Job::class);
        $job->shouldReceive('getJobId')->andReturn('uuid-123');
        $job->shouldReceive('resolveName')->andReturn('TestJob');
        $job->shouldReceive('getRawBody')->andReturn(json_encode(['data' => 'foo']));
        $job->shouldReceive('getConnectionName')->andReturn('sync');
        $job->shouldReceive('getQueue')->andReturn('default');
        $job->shouldReceive('attempts')->andReturn(1);

        // Manually trigger the event to see if reporter reacts
        event(new JobProcessing('sync', $job));

        Http::assertSent(function ($request) {
            return $request->url() === 'http://queuemaster.test/api/v1/jobs/events' &&
                   $request['uuid'] === 'uuid-123' &&
                   $request['status'] === 'running';
        });
    }

    public function test_it_does_not_send_payload_if_disabled()
    {
        Http::fake();
        config(['queuemaster.enabled' => false]);

        $job = \Mockery::mock(\Illuminate\Contracts\Queue\Job::class);
        $job->shouldReceive('getJobId')->andReturn('uuid-123');
        
        event(new JobProcessing('sync', $job));

        Http::assertNothingSent();
    }
}
