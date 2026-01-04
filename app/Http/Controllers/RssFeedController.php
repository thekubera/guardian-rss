<?php

namespace App\Http\Controllers;

use App\Services\GuardianApiService;
use App\Services\RssFeedBuilder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RssFeedController extends Controller
{
    public function __invoke(string $section)
    {
        $cacheKey = "rss_section_{$section}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($section) {

            Log::info('Fetching Guardian section', [
                'section' => $section,
            ]);
            $apiResponse = app(GuardianApiService::class)->fetchSection($section);

            return response(
                app(RssFeedBuilder::class)->build($apiResponse, ['self' => url($section)]),
                200
            )
                ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
        });
    }
}
