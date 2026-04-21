<?php

namespace QueueMaster\Reporter;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class QueueMasterReporter
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 5,
            'connect_timeout' => 2,
        ]);
    }

    public function send(array $payload)
    {
        $endpoint = config('queuemaster.api_endpoint');
        $token = config('queuemaster.api_token');

        if (empty($endpoint) || empty($token)) {
            Log::warning('QueueMaster: API endpoint or token is not configured.');
            return;
        }

        try {
            $this->client->post($endpoint, [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);
        } catch (\Exception $e) {
            // Fail silently to not disrupt the client application, but log it
            Log::error('QueueMaster: Failed to report job status. ' . $e->getMessage());
        }
    }
}
