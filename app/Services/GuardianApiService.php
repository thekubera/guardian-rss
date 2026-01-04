<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianApiService
{
    public function fetchSection(string $section): array
    {
        $response = Http::get('https://content.guardianapis.com/search', [
            'section' => $section,
            'api-key' => config('services.guardian.key'),
            'show-fields' => 'trailText,headline,shortUrl',
        ]);
        info($response);

        if ($response->failed()) {
            Log::error('Guardian API failed', [
                'section' => $section,
                'status' => $response->status()
            ]);

            abort(502, 'Failed to fetch Guardian content');
        }

        return $response->json();
    }
}
