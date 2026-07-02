<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Favorite;
use App\Models\User;

final class ToggleFavoriteAction
{
    public function handle(User $user, string $type, string $externalId, array $metadata = []): array
    {
        $favorite = $user->favorites()
            ->where('type', $type)
            ->where('external_id', $externalId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return ['status' => 'removed'];
        }

        $user->favorites()->create([
            'type' => $type,
            'external_id' => $externalId,
            'metadata' => $metadata,
        ]);

        return ['status' => 'added'];
    }
}
