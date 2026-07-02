<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateFavoriteAction;
use App\Actions\DeleteFavoriteAction;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()->favorites()->latest()->paginate(20);
        return FavoriteResource::collection($favorites)->response();
    }

    public function store(Request $request, CreateFavoriteAction $action): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'external_id' => 'required|string',
            'metadata' => 'nullable|array',
        ]);

        $favorite = $action->handle(
            $request->user(),
            $validated['type'],
            $validated['external_id'],
            $validated['metadata'] ?? []
        );

        return (new FavoriteResource($favorite))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, Favorite $favorite, DeleteFavoriteAction $action): JsonResponse
    {
        if ($favorite->user_id !== $request->user()->id) {
            abort(403);
        }
        $action->handle($request->user(), $favorite);
        return response()->json(null, 204);
    }
}
