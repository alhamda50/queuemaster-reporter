<?php

namespace QueueMaster\Reporter\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class RetryWebhookController extends Controller
{
    /**
     * Handle the retry request from QueueMaster Platform.
     */
    public function __invoke(Request $request)
    {
        // For a hackathon/machine test, we'll keep security simple: 
        // In production, you would use a signed signature check here.
        if ($request->header('X-QueueMaster-Token') !== config('queuemaster.api_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $jobClass = $request->input('name');
        $payload = $request->input('payload');

        if (!class_exists($jobClass)) {
            return response()->json(['error' => "Job class {$jobClass} no longer exists in this application."], 422);
        }

        try {
            // Re-dispatch the job
            $jobInstance = new $jobClass(...($payload ?? []));
            dispatch($jobInstance);

            return response()->json(['message' => 'Job successfully re-dispatched']);
        } catch (\Exception $e) {
            Log::error("QueueMaster Retry Failed: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
