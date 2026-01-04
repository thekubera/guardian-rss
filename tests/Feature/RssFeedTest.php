<?php

use Illuminate\Support\Facades\Http;

test('it returns an rss feed for a valid section', function () {
    Http::fake([
        'content.guardianapis.com/*' => Http::response([
            'response' => [
                'results' => [
                    [
                        'webTitle' => 'Politics test article',
                        'webUrl' => 'https://example.com/politics',
                        'webPublicationDate' => '2026-01-04T16:00:00Z',
                        'id' => 'politics/2026/01/04/test',
                        'fields' => [
                            'trailText' => 'Test description',
                        ],
                    ],
                ],
            ],
        ], 200),
    ]);

    $response = $this->get('/politics');

    $response
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
        ->assertSee('<rss', false)
        ->assertSee('<item>', false)
        ->assertSee('Politics test article', false);
});
