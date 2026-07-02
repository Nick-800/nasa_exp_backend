<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\ToggleFavoriteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()->favorites()->latest()->paginate(20);
        return response()->json($favorites);
    }

    public function toggle(Request $request, ToggleFavoriteAction $action): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'external_id' => 'required|string',
            'metadata' => 'nullable|array',
        ]);

        $result = $action->handle(
            $request->user(),
            $validated['type'],
            $validated['external_id'],
            $validated['metadata'] ?? []
        );

        return response()->json($result);
    }
}
