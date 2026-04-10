<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PreviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        // 1. Validate that the user sent a real URL
        $data = $request->validate([
            'url' => 'required|url'
        ]);

        try {
            // 2. Fetch the URL content
            $response = Http::timeout(10)->get($data['url']);
        } catch (ConnectionException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to reach the provided URL.'
            ], 502);
        }

        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch metadata from the provided URL.'
            ], 502);
        }

        // 3. For now, just return the raw body to prove it works
        return response()->json([
            'status' => 'success',
            'raw_html' => substr($response->body(), 0, 500) // First 500 chars
        ]);
    }
}
