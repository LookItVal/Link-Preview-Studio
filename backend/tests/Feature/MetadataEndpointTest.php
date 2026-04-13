<?php

use Illuminate\Support\Facades\Http;

$sampleHtml = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>Example Page</title>
    <meta name="description" content="An example page for testing.">
    <meta name="author" content="Test Author">
    <meta property="og:title" content="OG Example">
    <meta property="og:description" content="OG description here">
    <meta property="og:image" content="https://example.com/img.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Twitter Example">
    <link rel="icon" href="/favicon.ico">
    <link rel="canonical" href="https://example.com/">
</head>
<body></body>
</html>
HTML;

test('returns structured metadata for a valid url', function () use ($sampleHtml) {
    Http::fake([
        'https://example.com' => Http::response($sampleHtml, 200),
    ]);

    $response = $this->postJson('/api/metadata', ['url' => 'https://example.com']);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('data.title', 'Example Page')
        ->assertJsonPath('data.description', 'An example page for testing.')
        ->assertJsonPath('data.og.title', 'OG Example')
        ->assertJsonPath('data.twitter.card', 'summary_large_image')
        ->assertJsonPath('data.canonical', 'https://example.com/');
});

test('returns 422 when url is missing', function () {
    $response = $this->postJson('/api/metadata', []);

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error');
});

test('returns 422 when url is invalid', function () {
    $response = $this->postJson('/api/metadata', ['url' => 'not-a-url']);

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error');
});

test('returns 422 when url host cannot be resolved', function () {
    $response = $this->postJson('/api/metadata', ['url' => 'https://nonexistent-host-for-preview.invalid']);

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'The URL host could not be resolved.');
});

test('returns 422 when the remote URL returns a 4xx response', function () {
    Http::fake([
        'https://example.com/missing' => Http::response('', 404),
    ]);

    $response = $this->postJson('/api/metadata', ['url' => 'https://example.com/missing']);

    $response->assertStatus(422)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'The provided URL returned HTTP 404.');
});

test('returns 502 when the remote server returns a 5xx error', function () {
    Http::fake([
        'https://example.com/down' => Http::response('', 500),
    ]);

    $response = $this->postJson('/api/metadata', ['url' => 'https://example.com/down']);

    $response->assertStatus(502)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Upstream server error while fetching metadata (HTTP 500).');
});

test('returns 502 when the connection fails', function () {
    Http::fake([
        'https://example.com/unreachable' => fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'),
    ]);

    $response = $this->postJson('/api/metadata', ['url' => 'https://example.com/unreachable']);

    $response->assertStatus(502)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Unable to reach the provided URL: Connection refused');
});
