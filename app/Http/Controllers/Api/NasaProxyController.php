<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NasaApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NasaProxyController extends Controller
{
    public function __construct(private readonly NasaApiService $nasaApi) {}

    public function apod(Request $request): JsonResponse
    {
        $request->validate(['date' => 'sometimes|date_format:Y-m-d']);
        return response()->json($this->nasaApi->getApod($request->date));
    }

    public function eonet(): JsonResponse
    {
        return response()->json($this->nasaApi->getEonetEvents());
    }

    public function neo(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);
        return response()->json($this->nasaApi->getNeoFeed($request->start_date, $request->end_date));
    }

    public function spaceWeather(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:FLR,CME,GST',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ]);
        return response()->json($this->nasaApi->getSpaceWeather(
            $request->type, 
            $request->start_date, 
            $request->end_date
        ));
    }
}
