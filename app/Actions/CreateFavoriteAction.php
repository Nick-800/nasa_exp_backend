<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Favorite;
use App\Models\User;

final class CreateFavoriteAction
{
    public function handle(User $user, string $type, string $externalId, array $metadata = []): Favorite
    {
        return $user->favorites()->firstOrCreate(
            ['type' => $type, 'external_id' => $externalId],
            ['metadata' => $metadata]
        );
    }
}
