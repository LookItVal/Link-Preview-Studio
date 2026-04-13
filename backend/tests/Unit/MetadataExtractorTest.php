<?php

use App\Services\MetadataExtractor;

beforeEach(function () {
    $this->extractor = new MetadataExtractor();
});

test('extracts title', function () {
    $html = '<html><head><title>Hello World</title></head><body></body></html>';

    $result = $this->extractor->extract($html);

    expect($result['title'])->toBe('Hello World');
});

test('extracts description', function () {
    $html = '<html><head><meta name="description" content="A test page"></head></html>';

    $result = $this->extractor->extract($html);

    expect($result['description'])->toBe('A test page');
});

test('extracts standard meta tags', function () {
    $html = '<html><head>
        <meta name="author" content="John">
        <meta name="keywords" content="php,laravel">
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['meta'])->toHaveKey('author', 'John')
        ->toHaveKey('keywords', 'php,laravel');
});

test('extracts open graph tags', function () {
    $html = '<html><head>
        <meta property="og:title" content="OG Title">
        <meta property="og:description" content="OG Desc">
        <meta property="og:image" content="https://example.com/image.jpg">
        <meta property="og:type" content="website">
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['og'])->toBe([
        'title' => 'OG Title',
        'description' => 'OG Desc',
        'image' => 'https://example.com/image.jpg',
        'type' => 'website',
    ]);
});

test('extracts twitter card tags', function () {
    $html = '<html><head>
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Twitter Title">
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['twitter'])->toBe([
        'card' => 'summary_large_image',
        'title' => 'Twitter Title',
    ]);
});

test('extracts twitter tags using property attribute', function () {
    $html = '<html><head>
        <meta property="twitter:card" content="summary">
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['twitter'])->toHaveKey('card', 'summary');
});

test('twitter tags are excluded from generic meta', function () {
    $html = '<html><head>
        <meta name="twitter:card" content="summary">
        <meta name="author" content="John">
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['meta'])->not->toHaveKey('twitter:card')
        ->toHaveKey('author', 'John');
});

test('extracts icons', function () {
    $html = '<html><head>
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <link rel="apple-touch-icon" href="/apple-icon.png" sizes="180x180">
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['icons'])->toHaveCount(2);
    expect($result['icons'][0])->toMatchArray([
        'rel' => 'icon',
        'href' => '/favicon.ico',
        'type' => 'image/x-icon',
    ]);
    expect($result['icons'][1])->toMatchArray([
        'rel' => 'apple-touch-icon',
        'href' => '/apple-icon.png',
        'sizes' => '180x180',
    ]);
});

test('extracts canonical url', function () {
    $html = '<html><head><link rel="canonical" href="https://example.com/page"></head></html>';

    $result = $this->extractor->extract($html);

    expect($result['canonical'])->toBe('https://example.com/page');
});

test('returns nulls for missing tags', function () {
    $html = '<html><head></head><body></body></html>';

    $result = $this->extractor->extract($html);

    expect($result['title'])->toBeNull()
        ->and($result['description'])->toBeNull()
        ->and($result['canonical'])->toBeNull()
        ->and($result['meta'])->toBe([])
        ->and($result['og'])->toBe([])
        ->and($result['twitter'])->toBe([])
        ->and($result['icons'])->toBe([]);
});

test('handles malformed html gracefully', function () {
    $html = '<html><head><title>Broken<meta name="description" content="test"></head>';

    $result = $this->extractor->extract($html);

    expect($result)->toBeArray()
        ->toHaveKeys(['title', 'description', 'meta', 'og', 'twitter', 'icons', 'canonical']);
});
