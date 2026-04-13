<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;

class MetadataExtractor
{
    public function extract(string $html): array
    {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        return [
            'title' => $this->extractTitle($xpath),
            'description' => $this->extractDescription($xpath),
            'meta' => $this->extractMeta($xpath),
            'og' => $this->extractOpenGraph($xpath),
            'twitter' => $this->extractTwitter($xpath),
            'icons' => $this->extractIcons($xpath),
            'canonical' => $this->extractCanonical($xpath),
        ];
    }

    private function extractTitle(DOMXPath $xpath): ?string
    {
        $node = $xpath->query('//title')->item(0);

        return $node?->textContent ?: null;
    }

    private function extractDescription(DOMXPath $xpath): ?string
    {
        $node = $xpath->query('//meta[@name="description"]')->item(0);

        return $node?->getAttribute('content') ?: null;
    }

    private function extractMeta(DOMXPath $xpath): array
    {
        $tags = [];
        $nodes = $xpath->query('//meta[@name and @content]');

        foreach ($nodes as $node) {
            $name = $node->getAttribute('name');

            // Skip og: and twitter: — they have their own sections
            if (str_starts_with($name, 'twitter:')) {
                continue;
            }

            $tags[$name] = $node->getAttribute('content');
        }

        return $tags;
    }

    private function extractOpenGraph(DOMXPath $xpath): array
    {
        $tags = [];
        $nodes = $xpath->query('//meta[starts-with(@property, "og:")]');

        foreach ($nodes as $node) {
            $property = $node->getAttribute('property');
            $key = substr($property, 3); // strip "og:"
            $tags[$key] = $node->getAttribute('content');
        }

        return $tags;
    }

    private function extractTwitter(DOMXPath $xpath): array
    {
        $tags = [];
        // Twitter cards use both name= and property= depending on the site
        $nodes = $xpath->query('//meta[starts-with(@name, "twitter:") or starts-with(@property, "twitter:")]');

        foreach ($nodes as $node) {
            $attr = $node->getAttribute('name') ?: $node->getAttribute('property');
            $key = substr($attr, 8); // strip "twitter:"
            $tags[$key] = $node->getAttribute('content');
        }

        return $tags;
    }

    private function extractIcons(DOMXPath $xpath): array
    {
        $icons = [];
        $nodes = $xpath->query('//link[contains(@rel, "icon")]');

        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');

            if ($href) {
                $icons[] = [
                    'rel' => $node->getAttribute('rel'),
                    'href' => $href,
                    'sizes' => $node->getAttribute('sizes') ?: null,
                    'type' => $node->getAttribute('type') ?: null,
                ];
            }
        }

        return $icons;
    }

    private function extractCanonical(DOMXPath $xpath): ?string
    {
        $node = $xpath->query('//link[@rel="canonical"]')->item(0);

        return $node?->getAttribute('href') ?: null;
    }
}
