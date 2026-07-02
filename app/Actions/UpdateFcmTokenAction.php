<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class UpdateFcmTokenAction
{
    public function handle(User $user, string $fcmToken): User
    {
        $user->forceFill([
            'fcm_token' => $fcmToken,
        ])->save();

        return $user;
    }
}
