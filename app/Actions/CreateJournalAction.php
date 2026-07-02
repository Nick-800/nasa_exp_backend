<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Journal;
use App\Models\User;

final class CreateJournalAction
{
    public function handle(User $user, array $data): Journal
    {
        return $user->journals()->create([
            'title' => $data['title'],
            'content' => $data['content'],
            'is_public' => $data['is_public'] ?? false,
        ]);
    }
}
