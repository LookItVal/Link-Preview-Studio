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
            $response = Http::timeout(10)->followRedirects()->withHeaders([
                'User-Agent' => 'Link Preview Studio/1.0 (https://link-preview-studio.lookitval.com; qnncecil@gmail.com)',
            ])->get($request->validated('url'));
        } catch (ConnectionException $exception) {
            $statusCode = str_contains(strtolower($exception->getMessage()), 'timed out') ? 504 : 502;

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to reach the provided URL: '.$exception->getMessage(),
            ], $statusCode);
        }

        if ($response->failed()) {
            if ($response->clientError()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The provided URL returned HTTP '.$response->status().'.',
                ], 422);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Upstream server error while fetching metadata (HTTP '.$response->status().').',
            ], 502);
        }

        return response()->json([
            'status' => 'success',
            'data' => $extractor->extract($response->body()),
        ]);
    }
}
