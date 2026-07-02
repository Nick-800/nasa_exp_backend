<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Favorite;
use App\Models\User;

final class DeleteFavoriteAction
{
    public function handle(User $user, Favorite $favorite): void
    {
        if ($favorite->user_id === $user->id) {
            $favorite->delete();
        }
    }
}
