<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\UpdateProfileAction;
use App\Actions\UpdateFcmTokenAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserProfileController extends Controller
{
    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): JsonResponse
    {
        $user = $action->handle($request->user(), $request->validated());
        return (new UserResource($user))->response();
    }

    public function updateFcmToken(Request $request, UpdateFcmTokenAction $action): JsonResponse
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = $action->handle($request->user(), $request->fcm_token);
        return (new UserResource($user))->response();
    }
}
