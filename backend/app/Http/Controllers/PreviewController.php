<?php

namespace App\Http\Controllers;

use App\Http\Requests\MetadataRequest;
use App\Services\MetadataExtractor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class PreviewController
{
    public function __invoke(MetadataRequest $request, MetadataExtractor $extractor): JsonResponse
    {
        try {
            $response = Http::timeout(10)->get($request->validated('url'));
        } catch (ConnectionException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to reach the provided URL.',
            ], 502);
        }

        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to fetch metadata from the provided URL.',
            ], 502);
        }

        return response()->json([
            'status' => 'success',
            'data' => $extractor->extract($response->body()),
        ]);
    }
}
