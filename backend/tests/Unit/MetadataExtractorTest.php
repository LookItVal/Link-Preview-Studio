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
        ->and($result['jsonLd'])->toBe([])
        ->and($result['icons'])->toBe([]);
});

test('handles malformed html gracefully', function () {
    $html = '<html><head><title>Broken<meta name="description" content="test"></head>';

    $result = $this->extractor->extract($html);

    expect($result)->toBeArray()
        ->toHaveKeys(['title', 'description', 'meta', 'og', 'twitter', 'jsonLd', 'icons', 'canonical']);
});

test('extracts json-ld structured data', function () {
    $html = '<html><head>
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Article",
            "name": "Test Article",
            "headline": "Test Headline",
            "description": "A test article description",
            "image": "https://example.com/photo.jpg",
            "author": {
                "@type": "Person",
                "name": "John Doe"
            },
            "datePublished": "2026-01-15",
            "publisher": {
                "@type": "Organization",
                "name": "Example Corp"
            }
        }
        </script>
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['jsonLd'])->toHaveCount(1);
    expect($result['jsonLd'][0])->toBe([
        'type' => 'Article',
        'name' => 'Test Article',
        'headline' => 'Test Headline',
        'description' => 'A test article description',
        'image' => 'https://example.com/photo.jpg',
        'author' => 'John Doe',
        'datePublished' => '2026-01-15',
        'publisher' => 'Example Corp',
    ]);
});

test('extracts json-ld with @graph array', function () {
    $html = '<html><head>
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@graph": [
                {
                    "@type": "WebPage",
                    "name": "My Page",
                    "description": "Page description"
                },
                {
                    "@type": "Organization",
                    "name": "My Org"
                }
            ]
        }
        </script>
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['jsonLd'])->toHaveCount(2);
    expect($result['jsonLd'][0])->toBe([
        'type' => 'WebPage',
        'name' => 'My Page',
        'description' => 'Page description',
    ]);
    expect($result['jsonLd'][1])->toBe([
        'type' => 'Organization',
        'name' => 'My Org',
    ]);
});

test('extracts json-ld with image as string', function () {
    $html = '<html><head>
        <script type="application/ld+json">
        {"@type": "Article", "image": "https://example.com/img.jpg"}
        </script>
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['jsonLd'][0]['image'])->toBe('https://example.com/img.jpg');
});

test('extracts json-ld with image as ImageObject', function () {
    $html = '<html><head>
        <script type="application/ld+json">
        {"@type": "Article", "image": {"@type": "ImageObject", "url": "https://example.com/img.jpg"}}
        </script>
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['jsonLd'][0]['image'])->toBe('https://example.com/img.jpg');
});

test('extracts json-ld with image as array of urls', function () {
    $html = '<html><head>
        <script type="application/ld+json">
        {"@type": "Article", "image": ["https://example.com/a.jpg", "https://example.com/b.jpg"]}
        </script>
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['jsonLd'][0]['image'])->toBe('https://example.com/a.jpg');
});

test('handles multiple json-ld script blocks', function () {
    $html = '<html><head>
        <script type="application/ld+json">
        {"@type": "WebSite", "name": "Site A"}
        </script>
        <script type="application/ld+json">
        {"@type": "Article", "headline": "Article B"}
        </script>
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['jsonLd'])->toHaveCount(2);
    expect($result['jsonLd'][0]['name'])->toBe('Site A');
    expect($result['jsonLd'][1]['headline'])->toBe('Article B');
});

test('skips invalid json-ld blocks', function () {
    $html = '<html><head>
        <script type="application/ld+json">NOT VALID JSON</script>
        <script type="application/ld+json">
        {"@type": "Article", "name": "Valid"}
        </script>
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['jsonLd'])->toHaveCount(1);
    expect($result['jsonLd'][0]['name'])->toBe('Valid');
});

test('extracts json-ld with string author', function () {
    $html = '<html><head>
        <script type="application/ld+json">
        {"@type": "Article", "author": "Jane Smith"}
        </script>
    </head></html>';

    $result = $this->extractor->extract($html);

    expect($result['jsonLd'][0]['author'])->toBe('Jane Smith');
});
