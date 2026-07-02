<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\UpdateProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

final class UserProfileController extends Controller
{
    public function update(UpdateProfileRequest $request, UpdateProfileAction $action): JsonResponse
    {
        $user = $action->handle($request->user(), $request->validated());
        return (new UserResource($user))->response();
    }
}
