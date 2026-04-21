<?php

namespace QueueMaster\Reporter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queuemaster:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify the connection between this application and the QueueMaster server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing QueueMaster connection...');

        $serverUrl = config('queuemaster.server_url');
        $apiToken = config('queuemaster.api_token');

        if (empty($serverUrl)) {
            $this->error('✘ QUEUEMASTER_SERVER_URL is not set in your .env file');
            return 1;
        }

        if (empty($apiToken)) {
            $this->error('✘ QUEUEMASTER_API_TOKEN is not set in your .env file');
            return 1;
        }

        // Standardize URL
        $baseUrl = rtrim($serverUrl, '/');
        $endpoint = $baseUrl . '/api/v1/test-connection';

        $this->comment("Server: {$baseUrl}");
        $this->comment("Endpoint: {$endpoint}");

        try {
            $response = Http::withToken($apiToken)
                ->timeout(10)
                ->get($endpoint);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('✔ Connection successful!');
                $this->line('');
                $this->info("Organization: " . ($data['data']['organization'] ?? 'Unknown'));
                $this->info("Server Time: " . ($data['data']['connected_at'] ?? 'N/A'));
                $this->line('');
                $this->comment('This application is now ready to report job statuses to QueueMaster.');
                return 0;
            }

            if ($response->status() === 401 || $response->status() === 403) {
                $this->error('✘ Authentication failed.');
                $this->line('Check if your QUEUEMASTER_API_TOKEN is correct and hasn\'t been revoked.');
                return 1;
            }

            $this->error("✘ Server returned error: " . $response->status());
            if ($response->json('message')) {
                $this->line($response->json('message'));
            }
            
            return 1;

        } catch (\Exception $e) {
            $this->error('✘ Connection failed: ' . $e->getMessage());
            $this->line('Ensure your QUEUEMASTER_SERVER_URL is correct and reachable from this server.');
            return 1;
        }
    }
}
