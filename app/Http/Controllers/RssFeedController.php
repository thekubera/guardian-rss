<?php

namespace App\Http\Controllers;

use App\Services\GuardianApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RssFeedController extends Controller
{
    public function __invoke(string $section)
    {
        $cacheKey = "rss_section_{$section}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($section) {

            Log::info('Fetching Guardian section', [
                'section' => $section
            ]);
            $articles = (new GuardianApiService())->fetchSection($section);
            return response()->json($articles);

            // return response(

            //     Response::HTTP_OK,
            //     ['Content-Type' => 'application/rss+xml']
            // );
        });
    }
}
