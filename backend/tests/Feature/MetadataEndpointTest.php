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

test('returns 502 when the remote server returns an error', function () {
    Http::fake([
        'https://down.example.com' => Http::response('', 500),
    ]);

    $response = $this->postJson('/api/metadata', ['url' => 'https://down.example.com']);

    $response->assertStatus(502)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Unable to fetch metadata from the provided URL.');
});

test('returns 502 when the connection fails', function () {
    Http::fake([
        'https://unreachable.example.com' => fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'),
    ]);

    $response = $this->postJson('/api/metadata', ['url' => 'https://unreachable.example.com']);

    $response->assertStatus(502)
        ->assertJsonPath('status', 'error')
        ->assertJsonPath('message', 'Unable to reach the provided URL.');
});
