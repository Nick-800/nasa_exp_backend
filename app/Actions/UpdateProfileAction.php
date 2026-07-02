<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;

final class UpdateProfileAction
{
    public function handle(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }
}
