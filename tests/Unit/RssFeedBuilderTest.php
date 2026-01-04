<?php

use App\Services\RssFeedBuilder;

it('builds a valid rss feed', function () {
    $builder = app(RssFeedBuilder::class);

    $xml = $builder->build(
        fakeApiResponse(),
        ['self' => 'https://example.com/rss']
    );

    expect($xml)
        ->toBeString()
        ->toContain('<rss version="2.0"')
        ->toContain('<channel>')
        ->toContain('<title>The Guardian - Latest News</title>');
});

it('includes atom self link', function () {
    $builder = app(RssFeedBuilder::class);

    $xml = $builder->build(
        fakeApiResponse(),
        ['self' => 'https://example.com/rss']
    );

    expect($xml)->toContain(
        '<atom:link href="https://example.com/rss" rel="self" type="application/rss+xml" />'
    );
});

it('renders rss items', function () {
    $builder = app(RssFeedBuilder::class);

    $xml = $builder->build(
        fakeApiResponse(),
        ['self' => 'https://example.com/rss']
    );

    expect($xml)
        ->toContain('<item>')
        ->toContain('<title>Politics news</title>');
});

function fakeApiResponse(): array
{
    return [
        'response' => [
            'results' => [
                [
                    'webTitle' => 'Politics news',
                    'webUrl' => 'https://example.com/politics',
                    'webPublicationDate' => '2026-01-04T16:00:00Z',
                    'id' => 'politics/2026/01/04/test-article',
                    'fields' => [
                        'trailText' => 'Test description',
                    ],
                ],
            ],
        ],
    ];
}
