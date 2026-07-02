<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class NasaApiService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.nasa.gov';

    public function __construct()
    {
        $this->apiKey = config('services.nasa.key', 'DEMO_KEY');
    }

    public function getApod(?string $date = null): array
    {
        $cacheKey = $date ? "nasa:apod:{$date}" : "nasa:apod:today";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($date) {
            $query = ['api_key' => $this->apiKey];
            if ($date) {
                $query['date'] = $date;
            }

            return Http::get("{$this->baseUrl}/planetary/apod", $query)->throw()->json();
        });
    }

    public function getNeoFeed(string $startDate, string $endDate): array
    {
        $cacheKey = "nasa:neo:{$startDate}:{$endDate}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($startDate, $endDate) {
            return Http::get("{$this->baseUrl}/neo/rest/v1/feed", [
                'api_key' => $this->apiKey,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ])->throw()->json();
        });
    }

    public function getEonetEvents(): array
    {
        return Cache::remember('nasa:eonet:events', now()->addMinutes(15), function () {
            return Http::get('https://eonet.gsfc.nasa.gov/api/v3/events', [
                'status' => 'open',
                'limit' => 100,
            ])->throw()->json();
        });
    }

    public function getSpaceWeather(string $type, string $startDate, string $endDate): array
    {
        $cacheKey = "nasa:spaceweather:{$type}:{$startDate}:{$endDate}";
        
        return Cache::remember($cacheKey, now()->addHours(1), function () use ($type, $startDate, $endDate) {
            return Http::get("{$this->baseUrl}/DONKI/{$type}", [
                'api_key' => $this->apiKey,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])->throw()->json();
        });
    }
}
